<?php

namespace App\Exports;

use App\Models\Order;
// export function collection, headings, map
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Excel;
use App\Exports\OrderExport;

class OrdersExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // func collection : proses pengambilan data yang akan ditampilkan di excel
    public function collection()
    {
        return Order::with('user')->get();
    }
    //headings : nama-nama th dari file excel
    public function headings(): array
    {
        return [
            "Nama Pembeli", "Obat", "Total Bayar", "Kasir", "Tanggal"
        ];
    }
    //map : data yang akan dimunculkan di excelnya (sma kaya forecach di blade)
    public function map($item): array
    {
        $dataObat = '';
        foreach ($item->medicines as $value) {
        //ubah object/array data dri medicinesnya menjadi string dengan hasil: vitamin A (qty 2: Rp. 18000),
        $format = $value["name_medicine"]. " (qty" .$value['qty'] . " : Rp. " . number_format($value['sub_price']) . "),";
        $dataObat = $format;
    }
        return [
        $item->name_customer,
        $dataObat,
        $item->total_price,
        $item->user->name,
        \Carbon\Carbon::parse($item->created_at)->isoFormat($item->created_at),
        ];
    }
}
