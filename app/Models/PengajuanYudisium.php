<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanYudisium extends Model
{
    protected $table = 'pengajuan_yudisium';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_ujian' => 'date',
        'submitted_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(
            Mahasiswa::class,
            'nim',
            'nim'
        );
    }

    public function dokumen()
    {
        return $this->hasMany(
            PengajuanDokumen::class,
            'pengajuan_id'
        );
    }

    public function validasi()
    {
        return $this->hasMany(
            ValidasiField::class,
            'pengajuan_id'
        );
    }
}