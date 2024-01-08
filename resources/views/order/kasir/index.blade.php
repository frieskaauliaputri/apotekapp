@extends('layouts.template')

@section('content')
    <div class="container mt-3">
        <div class="d-flex justify-content-end">
            <a href="{{ route('kasir.order.create') }}" class="btn btn-primary">Pembelian Baru</a>
        </div>
        <br><br>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th class="text-center">
                        <th>Pembeli</th>
                        <th>Obat</th>
                        <th>Total Bayar</th>
                        <th>Kasir</th>
                        <th>Tanggal Pembelian</th>
                        <th></th>
                    </th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
               @foreach ($orders as $item )
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td>{{ $item['name_customer']}}</td>
                        <td>
                            {{-- karna column medicines pada table orders bertipe json yang diubah formatnya menjadi array, maka dari itu untuk mengakses/menampilkan itemnya perlu menggunakan looping --}}
                            @foreach ($item['medicines'] as $medicine)
                            <ol>
                                <li>
                                    {{-- mengakses key array assoc dari tiap item array value column medicine --}}
                                    {{ $medicine['name_medicine'] }} ( {{ number_format($medicine['price'],0,',','.') }} ) : Rp. {{ number_format ($medicine['sub_price'],0,',','.') }} <small>qty {{ $medicine['qty'] }}</small>
                                </li>
                            </ol>
                            @endforeach
                        </td>
                        <td>Rp. {{ number_format($item['total_price'],0,',','.') }}</td>
                        {{-- karena nama kasir terdapat pada table users, dan relasi antara order dan user telah didefinisikan pada function relasi bernama user, maka untuk mengakses column pada table users melalui relasi antara keduanya dapat dilakukan dengan $var ['namaFuncRelasi']['columnDariiTableLainnya'] --}}
                        <td>{{ $item['user']['name'] }}</td>
                        <td>
                            @php
                            // setting lokal time sebgai wilayah indonesia
                            setlocale(LC_ALL, 'IND');
                        @endphp
                        </td>
                        <td>
                            <a href="{{ route('kasir.order.download', $item['id']) }}" class="btn btn-secondary">Download Struk</a>
                        </td>
                    </tr>
               @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-end">
            {{-- jika data ada atau > 0 --}}
            @if ($orders->count())
            {{-- munculkan tampilan pagination --}}
                {{ $orders->links() }}

            @endif
        </div>
    </div>
@endsection
