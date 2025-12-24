<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
class DataController extends Controller
{
    // Trend Analizi ve Risk Hesaplama

    public function index()
    {
        return view('index');
    }
    public function downloadPdf()
    {
        $products = Product::with('transactions')->get();
        $pdf = Pdf::loadView('pdf.report', compact('products'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('urun-raporu.pdf');
    }
    public function analyze()
    {
        $products = Product::with('transactions')->get();

        $analysisResults = [];
        foreach ($products as $product) {
            // Alış ve satış trendlerini hesapla
            $transactions = $product->transactions->sortBy('date'); // Tarihe göre sırala
            $purchaseTrend = $this->calculateTrend($transactions, 'purchase_price');
            $saleTrend = $this->calculateTrend($transactions, 'sale_price');
            $demandTrend = $this->calculateTrend($transactions, 'demand');

            // Risk Seviyesini Belirleme
            $riskLevel = $this->determineRisk($purchaseTrend, $saleTrend, $demandTrend, $product->stock);

            $analysisResults[] = [
                'product' => $product,
                'purchase_trend' => $purchaseTrend,
                'sale_trend' => $saleTrend,
                'demand_trend' => $demandTrend,
                'risk_level' => $riskLevel,
            ];
        }

        return view('analysis', compact('analysisResults'));
    }

    // Trend Hesaplama (Mevcut dönemle bir önceki dönemi karşılaştırır)
    private function calculateTrend($transactions, $key)
    {
        $previousValue = null;
        $trends = [];
        foreach ($transactions as $transaction) {
            if ($previousValue !== null) {
                $change = (($transaction->$key - $previousValue) / $previousValue) * 100;
                $trends[] = round($change, 2); // Yüzde değişim
            }
            $previousValue = $transaction->$key;
        }
        return $trends;
    }

    // Risk Analizi
    private function determineRisk($purchaseTrend, $saleTrend, $demandTrend, $stock)
    {
        if (end($purchaseTrend) > 15 && end($saleTrend) < -10 && end($demandTrend) > 20) {
            return '🔴 Yüksek Risk';
        } elseif ($stock < 10) {
            return '🟡 Orta Risk (Stok Az)';
        } else {
            return '🟢 Düşük Risk';
        }
    }
}