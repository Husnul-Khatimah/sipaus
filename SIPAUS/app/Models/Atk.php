<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atk extends Model
{
    use HasFactory;

    protected $table = 'atk';
    protected $primaryKey = 'id_atk'; // Pastikan ini sesuai dengan migrasi

    protected $fillable = [
        'nama_barang',
        'stok',
        'satuan',
        'jenis_atk_id', // Foreign key ke jenis_atk
    ];

    // Relasi: ATK dimiliki oleh satu JenisAtk (Kategori)
    public function jenisAtk()
    {
        return $this->belongsTo(JenisAtk::class, 'jenis_atk_id', 'id_jenis_atk');
    }
    // ... (relasi lainnya jika ada) ...
}
