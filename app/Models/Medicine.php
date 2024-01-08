<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    //properti yang digunakan untuk menyimpan nam-nama colum yang bisa diisi valuenya
    protected $fillable = [
        'type',
        'name',
        'price',
        'stock',
    ];
}
