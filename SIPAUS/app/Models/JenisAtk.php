<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAtk extends Model
{
    use HasFactory;

    protected $table = 'jenis_atk';
    protected $primaryKey = 'id_jenis_atk'; // Tetap ini

    protected $fillable = [
        'nama_kategori', // <--- Ganti 'jenis_atk' menjadi 'nama_kategori'
    ];

    // Jika Anda tidak menggunakan timestamps (created_at, updated_at) di tabel ini,
    // tambahkan baris berikut:
    // public $timestamps = false;

    // Relasi: Kategori memiliki banyak ATK
    public function atks()
    {
        return $this->hasMany(Atk::class, 'jenis_atk_id', 'id_jenis_atk');
    }
}
