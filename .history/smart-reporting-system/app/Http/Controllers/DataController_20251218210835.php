<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use App\Models\Transaction; // Model dosyaları
use App\Imports\TransactionImport; // İçe aktarım için oluşturulacak sınıf

class DataController extends Controller
{
    // Ana sayfa
    public function index()
    {
        return view('index');
    }

    // Dosya yükleme ve işleme
    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new TransactionImport, $request->file('file'));

        return redirect()->route('showResults')->with('success', 'Dosya başarıyla yüklendi!');
    }

    // Sonuçları göster
    public function showResults()
    {
        $products = Product::with('transactions')->get(); // Veriler ilişkili şekilde çek
        return view('results', compact('products'));
    }
}