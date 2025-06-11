<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanAtk extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'atk_id',
        'jumlah_pesan',
        'tanggal_pesan',
        'status',
        'nama_supplier', // PENTING: Tambahkan ini jika Anda menyimpan nama supplier sebagai string
        'nama_barang',   // PENTING: Tambahkan ini jika Anda menyimpan nama barang sebagai string
    ];

    // Relasi ke tabel users untuk supplier
    public function supplier()
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    // Relasi ke tabel atks untuk barang yang dipesan
    public function atk()
    {
        return $this->belongsTo(Atk::class);
    }
}
