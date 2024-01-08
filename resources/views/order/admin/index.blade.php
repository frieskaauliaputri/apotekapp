@extends('layouts.template')

@section('content')
    <div class="my-5 d-flex justify-content-end">
        <a href="{{ route('order.export-excel')}}" class="btn btn-primary">Export Data (excel)</a>
    </div>
    <form action="{{ route('order.search') }}">
        <button type="button" class="btn btn-light">
            <input type="date" name="search_date" class="form-control">
        </button>
        <button class="btn btn-primary">Cari data</button>


    </form>
    <br>
    <table class="table table-stripped table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Pembeli</th>
                <th>Obat</th>
                <th>Kasir</th>
                <th>Tanggal Pembelian</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    {{-- menampilkan angka urutan berdasarkan page pagination (digunakan ketika mengambil adta dengan paginate/simplePaginate) --}}
                    <td>{{ ($orders->currentpage()-1) * $orders->perpage() + $loop->index + 1 }}</td>
                    <td>{{ $order->name_customer }}</td>
                    <td>
                        {{-- nested loop : didalam looping ada looping --}}
                        {{-- karan acolumn medicines tipe datanya berbentuk array json, maka untuk mengaksesnya perlu di loopingjuga --}}
                        <ol>
                            @foreach ($order['medicines'] as $medicine)
                                <li>
                                    {{-- hasil yang diinginkan --}}
                                    {{-- 1. nama obat (Rp. 3000) : Rp 15000 qty 5 --}}
                                    {{ $medicine['name_medicine'] }}
                                    ( Rp. {{ number_format($medicine['price'],0,',','.') }} );
                                    Rp. {{ number_format($medicine['sub_price'],0,',','.') }}
                                    <small>qty {{ $medicine['qty'] }}</small>
                                </li>
                            @endforeach
                        </ol>
                    </td>
                    <td>{{ $order['user']['name'] }}</td>
                    {{-- carbon : package bawaan laravel untuk mengatur hal-hal yang berkaitan dengan tipe data date/ datetime --}}
                    @php
                        // setting lokal time sebgai wilayah indonesia
                        setlocale(LC_ALL, 'IND');
                    @endphp
                    <td>{{ Carbon\Carbon::parse($order->created_at)->formatLocalized('%d %B %y') }}</td>
                    <td><a href="{{ route('order.download', $order['id']) }}" class="btn btn-secondary">Unduh (.pdf)</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endsection
