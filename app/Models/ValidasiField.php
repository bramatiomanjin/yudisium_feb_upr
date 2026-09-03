<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiField extends Model
{
    protected $table = 'validasi_field';
    protected $guarded = ['id']; // Mengizinkan semua kolom diisi kecuali 'id'

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanYudisium::class, 'pengajuan_id');
    }
}