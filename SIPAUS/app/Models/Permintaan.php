<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permintaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'atk_id',
        'jumlah',
        'tanggal_permintaan',
        'tempat',
        'keterangan',
        'status', // Pastikan kolom 'status' ada di migrasi Anda
    ];

    // Definisi relasi dengan Model User (pemohon)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Definisi relasi dengan Model Atk (barang yang diminta)
    public function atk()
    {
        return $this->belongsTo(Atk::class);
    }
}
