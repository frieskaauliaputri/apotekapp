@extends('layouts.template')

@section('content')
    <form action="{{ route('user.update', $user['id']) }}" method="POST" class="card p-5">
        @csrf
        @method('PATCH')

        @if ($errors->any())
	    <ul class="alert alert-danger P-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>       
        @endif

        <div class="mb-3 row">
            <label for="name" calss="col-sm-2 col-form-label">Nama :</label>
            <div calss="col-sm-10">
                <input type="text" class="form-control" id="name" name="name" value="{{ $user['name'] }}">  
            </div>
        </div>
        <div class="mb-3 row">
            <label for="name" calss="col-sm-2 col-form-label">Email :</label>
            <div calss="col-sm-10">
                <input type="email" class="form-control" id="email" name="email" value="{{ $user['email'] }}">  
            </div>
        </div>
        <div class="mb-3 row">
            <label for="type" calss="col-sm-2 col-form-label">Tipe Pengguna :</label>
            <div calss="col-sm-10">
                <select class="form-select" name="role" id="role">
                    <option selected disabled hidden>Pilih </option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                </select>   
            </div>
        </div>
        <div class="mb-3 row">
        <label for="password" class="col-sm-2 col-form-label">Ubah Password :</label>
        <div class="col-sm-10">
            <input type="password" class="form-control" id="password" name="password" value="{{ $user['password'] }}">
        </div>
        </div>
         
        <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
        </div>
        
    </form>
@endsection