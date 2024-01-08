<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\OrdersExport;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // mengambil seluruh data pada table orders dengan pagination per halaman 10 data serta mengambil hasil data relasi function bernama user pada model Order
        $orders = Order::with('user')->simplePaginate(10);
        return view("order.kasir.index", compact("orders"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = Medicine::all();
        return view("order.kasir.create", compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_customer' => 'required',
            'medicines' => 'required',
        ]);
        //mencari jumlah item yang sama pada array, struknya :
        // ["item" => "jumlah]
        $arrayDistinct = array_count_values($request->medicines);
        // menyiapkan array kosong untuk menampung format array baru
        $arrayAssoMedicines = [];
        // looping hasil penghitungan item distinct (duplikat)
        // key akan berupa value dri input medicines (id), item array berupa jumlah penghitungan item duplikat

        foreach($arrayDistinct as $id => $count) {
            //mencari data obat berdasarkan id (obat yang dipilih)
            $medicine = Medicine::where('id', $id)->first();
            // ambil bagian column price dri hasil pencaharian lalu kalikan dengan jumlah item duplikat sehingga akan mengahsilkan total harga dri pembelian obat tersebut
            $subPrice = $medicine['price'] * $count;
            // struktur value column medicines menjadi multidimensi dengan dimensi kedua berbentuk array assoc dengan key "id", "name_medicine","qty","price"
            $arrayItem = [
                "id" => $id,
                "name_medicine" => $medicine['name'],
                "qty" => $count,
                "price" => $medicine['price'],
                "sub_price" => $subPrice,
            ];
            // masukkan struktur array tersebut ke array kosong yang disediakan sebelumnya
            array_push($arrayAssoMedicines, $arrayItem);
        }
        // total harga pembelian dari obat-obat yang dipilih
        $totalPrice = 0;
        //looping format array medicines baru
        foreach( $arrayAssoMedicines as $item) {
            //total harga pembelian ditambahkan dri keseluruhan sub_price data medicines
            $totalPrice += (int)$item['sub_price'];
        }
        //harga beli ditambah 10% ppn
        $princeWithPPN = $totalPrice + ($totalPrice * 0.01);
        //tambah data ke database
        $proses = Order::create([
            // data user_id diambil dari id akun kasir yang sedanglogin
            'user_id' => Auth::user()->id,
            'medicines' => $arrayAssoMedicines,
            'name_customer' =>$request->name_customer,
            'total_price' => $princeWithPPN,
        ]);

        if ($proses) {
            //jika proses tambah data berhasil, ambil data order yang dibuat oleh kasir yang sedang login (where), dengan tanggal paling terbaru (orderBy), ambil hanya satu data (first)
            $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'DESC')->first();
            //kirim data order yang diambil tdi, bagian column id parameter path dari route print
            return redirect()->route('kasir.order.print', $order['id']);
        }else{
            // jika tidak berhasil, maka diarahkan kembali ke halaman form dengan pesan pemberitahuan
            return redirect()->back()->with('failed', 'Gagal membuat data pembelian, Silahkan coba kembali dengan sesuai!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        $order = Order::find($id);
        return view('order.kasir.print', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }

    public function downloadPDF($id)
    {
        // ambil data yang diperlukan, dan pastikan data berformat array
        $order = Order::find($id)->toArray();
        //mengirim inisial variable dari data yang akan digunakan pada layout pdf
        view()->share('order', $order);
        // panggil blade yang akan di download
        $pdf = PDF::loadView('order.kasir.download-pdf', $order);
        // kembalikan atau hasilkan bentuk pdf dengan nama file tertentu
        return $pdf->download('receipt.pdf');
    }

    public function data()
    {
        // with: mengambil hasil relasi dari PK dan FKnya, valuenya == nama func relasi hasMany/belongsTo yang ada modelnya
        $orders = Order::with('user')->simplePaginate(5);
        return view("order.admin.index", compact('orders'));
    }

    public function exportExcel()
    {
        $file_name = 'data_pembelian'.'.xlsx';

        return Excel::download(new OrdersExport, $file_name);
    }

    public function search(Request $request)
    {
        $orders = Order::with('user');
        // periksa apakah ada tanggal pencaharian
        if($request->has('search_date')) {
            $searchDate = $request->input('search_date');
            $orders->whereDate('created_at', $searchDate);
        }

        $orders = $orders->simplePaginate(6);
        if (Auth::user()->role == 'admin') {
            return view("order.admin.index", compact("orders"));
        }else{
            return view("order.kasir.index", compact("orders"));
        }
    }
}

