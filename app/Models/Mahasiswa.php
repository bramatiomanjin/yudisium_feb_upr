<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Izinkan semua kolom diisi secara massal
    protected $guarded = [];

    // Relasi 1 mahasiswa punya 1 pengajuan yudisium aktif
    public function pengajuan()
    {
        return $this->hasOne(PengajuanYudisium::class, 'nim', 'nim');
    }
}