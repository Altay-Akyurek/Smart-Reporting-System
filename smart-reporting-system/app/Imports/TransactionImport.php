<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TransactionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // İlk adımda yeni bir ürün oluşturulabilir veya var olan ürüne ilişkilendirilebilir
        $product = Product::firstOrCreate(
            ['name' => $row['urun']], // Excel başlığına bağlı
            ['purchase_price' => $row['alis'], 'sale_price' => $row['satis'], 'stock' => $row['stok']]
        );

        // Transaction ekle
        return new Transaction([
            'product_id' => $product->id,
            'date' => \Carbon\Carbon::createFromFormat('Y-m', $row['tarih']),
            'demand' => $row['talep'],
        ]);
    }
}