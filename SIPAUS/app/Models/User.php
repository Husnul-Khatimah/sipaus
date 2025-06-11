<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Opsional, jika Anda ingin fitur verifikasi email
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Penting: alias User ke Authenticatable
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Opsional, jika pakai API tokens

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Tambahkan 'role' di sini, pastikan ada di migrasi users
        'nip', // PENTING: Tambahkan ini jika Anda menggunakan NIP di tabel pengguna/user
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel 10+ otomatis menghash password jika di-cast
    ];

    // Relasi jika user memiliki banyak permintaan
    public function permintaans()
    {
        return $this->hasMany(Permintaan::class);
    }

    // Relasi jika user memiliki banyak pesanan (sebagai supplier)
    public function pesanAtks()
    {
        return $this->hasMany(PesanAtk::class, 'supplier_id');
    }

    // Relasi jika user memiliki banyak survei
    public function survais()
    {
        return $this->hasMany(Survai::class);
    }
}
