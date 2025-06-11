<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survai extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feedback',
        'rating',
        'tanggal_survei',
        'status', // PENTING: Tambahkan ini jika Anda menggunakan kolom status untuk survei (approve/reject)
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
