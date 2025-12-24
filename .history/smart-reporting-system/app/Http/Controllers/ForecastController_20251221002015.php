<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Phpml\Regression\LeastSquares; // Doğrusal Regresyon sınıfı
use Barryvdh\DomPDF\Facade\Pdf;
class ForecastController extends Controller
{
    // Stok / Talep Tahmini Oluşturma
    public function forecast()
    {
        $products = Product::with('transactions')->get(); // Ürün ve hareketleri al
        $forecastResults = [];

        foreach ($products as $product) {
            $transactions = $product->transactions->sortBy('date'); // Tarihe göre sıralı al

            // Girdi (X) ve çıktı (Y) verilerini hazırla
            $x = []; // Zaman - Örneğin (ay 1, ay 2 ...)
            $y = []; // Tahmin edilecek talep/stok değerleri (ör: ürün talebi)

            foreach ($transactions as $index => $transaction) {
                $x[] = [$index + 1]; // Ay numarası
                $y[] = $transaction->demand; // Talep miktarı
            }

            // Doğrusal Regresyon modeli oluştur
            $regression = new LeastSquares();
            $regression->train($x, $y);

            // Gelecek bir ayın tahmini (ay numarasını artırarak ekleyelim)
            $nextMonth = count($transactions) + 1;
            $forecastedDemand = $regression->predict([$nextMonth]);

            $forecastResults[] = [
                'product' => $product->name,
                'current_demand' => end($y),
                'forecasted_demand' => round($forecastedDemand, 2), // Tahmini talep
            ];
        }

        return view('forecast', compact('forecastResults'));
    }
    private function calculateMovingAverage($transactions, $period = 3)
    {
        $data = $transactions->pluck('demand')->toArray(); // Talep verilerini al
        $movingAverages = []; // Ortalamalar için liste

        for ($i = 0; $i <= count($data) - $period; $i++) {
            $chunk = array_slice($data, $i, $period); // Belirli dönemlik (ör: son 3 ay) kesit
            $movingAverages[] = array_sum($chunk) / count($chunk); // Ortalama hesapla
        }

        return end($movingAverages); // Son hareketli ortalama
    }
    public function generatePdf()
    {
        $products = Product::with('transactions')->get(); // Verileri çek
        $forecastResults = [];

        foreach ($products as $product) {
            $transactions = $product->transactions->sortBy('date');
            $x = [];
            $y = [];

            foreach ($transactions as $index => $transaction) {
                $x[] = [$index + 1];
                $y[] = $transaction->demand;
            }

            if (count($x) > 0 && count($y) > 0) {
                $regression = new LeastSquares();
                $regression->train($x, $y);
                $nextMonth = count($transactions) + 1;
                $forecastedDemand = $regression->predict([$nextMonth]);

                $forecastResults[] = [
                    'product' => $product->name,
                    'current_demand' => end($y),
                    'forecasted_demand' => round($forecastedDemand, 2),
                ];
            }
        }

        // PDF oluşturma
        $pdf = Pdf::loadView('pdf.forecast', compact('forecastResults'))->setPaper('a4', 'landscape');
        return $pdf->download('tahmin_raporu.pdf'); // PDF dosyasını indir
    }
}