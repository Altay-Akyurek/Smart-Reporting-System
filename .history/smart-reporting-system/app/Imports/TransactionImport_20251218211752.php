<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Product;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class TransactionImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        //işk adım yeni bir ürün oluşturulabilir veya var olan ürüne ilişkilendşirilebilri
        $product = Product::firsOrCreate(
            ['name' => $row['urun']],//Excel Başlığına bağlı
            ['purchase_price' => $row['alis'], 'sale_price' => $row['satis'], 'stock' => $row['stock']]
        );
        //Transaction Ekle
        return new Transaction([
            'product_id' => $product->id,
            'date' => \Carbon\Carbon::createFromFormat('Y-m', $row['tarih']),
            'demand' => $row['talep']
        ]);
    }
}
