<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatRevisi extends Model
{
    protected $table = 'riwayat_revisi';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanYudisium::class, 'pengajuan_id');
    }
}