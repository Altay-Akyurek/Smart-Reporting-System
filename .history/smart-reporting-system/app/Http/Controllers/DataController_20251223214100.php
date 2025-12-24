<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;

use App\Services\AnalysisService;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;

class DataController extends Controller
{
    protected $analysisService;

    public function __construct(AnalysisService $analysisService)
    {
        $this->analysisService = $analysisService;
    }

    public function index()
    {
        return view('index');
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt'
        ]);

        $file = $request->file('file');

        // Simple array parsing
        $data = Excel::toArray([], $file);

        // CLEAR OLD SESSION DATA to prevent stale results
        Session::forget(['analysis_results', 'group_results', 'correlations', 'temp_data', 'temp_headers', 'detected_date_column', 'temp_date_values', 'result_dates']);

        if (empty($data)) {
            return back()->withErrors(['file' => 'Dosya boş veya okunamadı.']);
        }

        // Identify numeric columns across ALL sheets
        $numericData = [];
        $headerTypes = [];

        foreach ($data as $sheetIndex => $rows) {
            if (empty($rows))
                continue;

            // Determine sheet name (Excel::toArray doesn't give names directly in this simple mode, so we use index or try to guess)
            // For better names, we could use WithMultipleSheets concern, but for now Index is reliable.
            $sheetName = "Sayfa " . ($sheetIndex + 1);

            $headers = array_shift($rows); // First row as headers

            foreach ($headers as $index => $header) {
                if (empty($header))
                    continue; // Skip empty headers

                $uniqueHeader = $sheetName . " - " . $header; // Prefix with sheet name
                $columnData = array_column($rows, $index);

                // Check if column is mostly numeric
                $numericCount = count(array_filter($columnData, 'is_numeric'));

                if (count($columnData) > 0 && $numericCount > count($columnData) * 0.5) {
                    $numericData[$uniqueHeader] = array_map('floatval', array_filter($columnData, 'is_numeric'));
                    $headerTypes[$uniqueHeader] = 'Sayısal';
                } else {
                    $headerTypes[$uniqueHeader] = 'Metin';

                    // Simple Date Detection logic
                    // If header contains "tarih" or "date" OR if we check first value format (omitted for simplicity/speed)
                    if (str_contains(mb_strtolower($header), 'tarih') || str_contains(mb_strtolower($header), 'date') || str_contains(mb_strtolower($header), 'zaman')) {
                        // Store candidate date column
                        // We only need one primary date column usually, but let's store key
                        Session::put('detected_date_column', $uniqueHeader);
                        Session::put('temp_date_values', $columnData); // Store values for X-axis
                        $headerTypes[$uniqueHeader] = 'Tarih/Zaman 📅';
                    }
                }
            }
        }

        if (empty($numericData) && empty($headerTypes)) {
            return back()->withErrors(['file' => 'Dosyada okunabilir veri bulunamadı.']);
        }

        // Store everything in session mainly for the next step (selection)
        // Store ALL data (numeric + text) to allow categorical selection for grouping
        // We previously only stored numericData, now we mix them.
        $allColumnData = [];
        // First, check numeric data we already found
        foreach ($numericData as $key => $val) {
            $allColumnData[$key] = $val;
        }

        // Now re-iterate efficiently to capture text columns we might have missed in the numeric loop
        // OR just optimize the original loop.
        // Optimization: refactor the original loop loop to store ALL valid columns.
        $finalStoredData = [];
        foreach ($data as $sheetIndex => $rows) {
            if (empty($rows))
                continue;
            $sheetName = "Sayfa " . ($sheetIndex + 1);
            $headers = array_shift($rows);
            foreach ($headers as $index => $header) {
                if (empty($header))
                    continue;
                $uniqueHeader = $sheetName . " - " . $header;
                $columnData = array_column($rows, $index);
                // Store Raw Data for everyone (needed for text categories)
                // But for numeric, we want floatval.
                if (isset($headerTypes[$uniqueHeader]) && $headerTypes[$uniqueHeader] === 'Sayısal') {
                    $finalStoredData[$uniqueHeader] = array_map('floatval', array_filter($columnData, 'is_numeric'));
                } else {
                    $finalStoredData[$uniqueHeader] = $columnData;
                }
            }
        }

        Session::put('temp_data', $finalStoredData);
        Session::put('temp_headers', $headerTypes);

        return redirect()->route('selectColumns');
    }

    public function showColumnSelection()
    {
        $headers = Session::get('temp_headers', []);

        if (empty($headers)) {
            return redirect()->route('home')->withErrors(['file' => 'Lütfen önce bir dosya yükleyin.']);
        }

        return view('select_columns', compact('headers'));
    }

    public function processSelectedColumns(Request $request)
    {
        $selectedColumns = $request->input('columns', []);
        $allData = Session::get('temp_data', []);

        if (empty($selectedColumns)) {
            return back()->withErrors(['columns' => 'Lütfen en az bir sütun seçin.']);
        }

        // Filter data based on selection
        $finalData = array_intersect_key($allData, array_flip($selectedColumns));

        // Calculate Correlations and Hypothesis Tests if more than 1 column
        $correlations = [];
        $hypothesisResults = [];
        $columnsList = array_keys($finalData);
        if (count($columnsList) > 1) {
            for ($i = 0; $i < count($columnsList); $i++) {
                for ($j = $i + 1; $j < count($columnsList); $j++) {
                    $col1 = $columnsList[$i];
                    $col2 = $columnsList[$j];
                    $val1 = array_values($finalData[$col1]);
                    $val2 = array_values($finalData[$col2]); // Re-index arrays to match

                    // Arrays might be different lengths if original data had empty cells, though our parser tries to be consistent.
                    // For safety, slice to shorter length
                    $len = min(count($val1), count($val2));
                    $v1 = array_slice($val1, 0, $len);
                    $v2 = array_slice($val2, 0, $len);

                    // Correlation
                    $score = $this->analysisService->calculateCorrelation($v1, $v2);
                    $interpretation = $this->analysisService->interpretCorrelation($score);

                    $correlations[] = [
                        'pair' => "$col1 <-> $col2",
                        'score' => $score,
                        'interpretation' => $interpretation
                    ];

                    // Hypothesis Testing (T-Test)
                    $tTestResult = $this->analysisService->performTTest($v1, $v2);
                    $hypothesisReport = $this->analysisService->generateHypothesisReport($col1, $col2, $tTestResult, $v1, $v2);

                    if ($hypothesisReport) {
                        $hypothesisResults[] = [
                            'pair' => "$col1 vs $col2",
                            'report' => $hypothesisReport
                        ];
                    }
                }
            }
        }

        $analysisResults = [];
        foreach ($finalData as $columnName => $values) {
            $stats = $this->analysisService->calculateStatistics($values);
            $trends = $this->analysisService->calculateTrend($values);
            $risk = $this->analysisService->determineRisk($trends, $stats);
            $decision = $this->analysisService->generateDecision($stats, $trends);
            $interpretation = $this->analysisService->interpretResults($stats);
            $futurePrediction = $this->analysisService->predictFuture($values);

            // Advanced Statistics
            $variance = $this->analysisService->calculateVariance($values);
            $quartiles = $this->analysisService->calculateQuartiles($values);
            $outliers = $this->analysisService->detectOutliers($values, $quartiles);
            $regression = $this->analysisService->performRegression($values);
            $histogram = $this->analysisService->calculateHistogram($values);

            $analysisResults[$columnName] = [
                'stats' => $stats,
                'trends' => $trends,
                'risk' => $risk,
                'decision' => $decision,
                'interpretation' => $interpretation,
                'future_prediction' => $futurePrediction,
                'values' => $values,

                // New Advanced Data
                'variance' => $variance,
                'quartiles' => $quartiles,
                'outliers' => $outliers,
                'regression' => $regression,
                'histogram' => $histogram
            ];
        }

        // Group Analysis (ANOVA-style)
        // Identify Categorical Columns among selection (those NOT in analysisResults keys but in finalData?)
        // Actually finalData contains everything selected. We need to check if they are numeric.
        $groupResults = [];
        $numericCols = [];
        $categoricalCols = [];

        foreach ($finalData as $col => $val) {
            // Check if it was treated as numeric analysis
            if (isset($analysisResults[$col])) {
                $numericCols[] = $col;
            } else {
                $categoricalCols[] = $col;
            }
        }

        if (!empty($categoricalCols) && !empty($numericCols)) {
            foreach ($categoricalCols as $catCol) {
                foreach ($numericCols as $numCol) {
                    $groupedStats = $this->analysisService->groupStatistics($finalData[$catCol], $finalData[$numCol]);
                    if (!empty($groupedStats)) {
                        // Use associative array for View compatibility (View expects $title => $stats)
                        $groupResults["$numCol by $catCol"] = $groupedStats;
                    }
                }
            }
        }

        // Calculate Correlation Matrix and Insights
        $correlationMatrix = [];
        $relationshipInsights = [];

        // We only want numeric columns for correlation
        $numericColumnsData = [];
        foreach ($finalData as $col => $val) {
            if (isset($analysisResults[$col])) { // If it was analyzed as numeric
                $numericColumnsData[$col] = $val;
            }
        }

        if (count($numericColumnsData) > 1) {
            $correlationMatrix = $this->analysisService->calculateCorrelationMatrix($numericColumnsData);
            $relationshipInsights = $this->analysisService->generateRelationshipInsights($correlationMatrix);
        }

        Session::put('analysis_results', $analysisResults);
        Session::put('group_results', $groupResults);
        Session::put('correlation_matrix', $correlationMatrix);     // NEW: Matrix
        Session::put('relationship_insights', $relationshipInsights); // NEW: Insights
        Session::put('hypothesis_results', $hypothesisResults);

        if (Session::has('temp_date_values')) {
            Session::put('result_dates', Session::get('temp_date_values'));
        }

        return redirect()->route('showResults');
    }

    public function showResults()
    {
        $results = Session::get('analysis_results', []);

        // If empty, try to fetch from DB as fallback (old method logic)
        if (empty($results)) {
            // Fallback logic could go here or just return empty view
        }

        return view('results', compact('results'));
    }

    public function downloadPdf(Request $request)
    {
        // Get session data
        $results = Session::get('analysis_results', []);

        // Get charts data from POST request (base64 images)
        $charts = [];
        if ($request->has('charts')) {
            $chartsJson = $request->input('charts');
            $charts = json_decode($chartsJson, true) ?? [];
        }

        if (!empty($results)) {
            $pdf = Pdf::loadView('pdf.report_generic', compact('results', 'charts'))
                ->setPaper('a4', 'landscape');
            return $pdf->download('analiz-raporu.pdf');
        }

        $products = Product::with('transactions')->get();
        $pdf = Pdf::loadView('pdf.report', compact('products', 'charts'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('urun-raporu.pdf');
    }

    // Keep old analyze method for backward compatibility if needed, or remove
    public function analyze()
    {
        // Redirect to generic results if using new flow
        return redirect()->route('showResults');
    }
}