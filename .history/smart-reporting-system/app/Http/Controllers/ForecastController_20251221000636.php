<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Phpml\Regression\LeastSquares;//Doğrusal Regresyon Sınıfı.

class ForecastController extends Controller
{
    //Stok / Talep Tahmin Oluşturma
    public function forecast()
    {
        $products = Product::with('transactions')->get();//Ürün ve hareketleri al
        $forecastResults = [];

        foreach ($products as $product) {
            $transactions = $product->transactions->sortBy('date');//Tarihe Göre Sırala

            //Gİrdi (x) Ve cıktı(y) veirlerini hazırlar
            $x = [];//Zaman - Örnegi (ay 1,ay 2)
            $y = [];//Tahmin edilecek talep/stok değerleri(ör:ürün talebi)

            foreach ($transactions as $index => $transaction) {
                $x[] = [$index + 1];//Ay numarası 
                $y[] = $transaction->demand;//Talep miktarı
            }

            //doğrusal Regresyon modeli oluşturma
            $regression = new LeastSquares();
            $regression->train($x, $y);

            //Gelecek bir ayın tahmini(ay numarası artırarak ekleyelim)
            $nextMount = count($transaction) + 1;
            $forecastedDemand = $regression->predict([$nextMount]);


            $forecastResults[] = [
                'product' => $product->name,
                'current_demand' => end($y),
                'forecasted_demand' => round($forecastedDemand, 2),
            ];
        }
        return view('forecast', compact('forecastResults'));
    }
}
