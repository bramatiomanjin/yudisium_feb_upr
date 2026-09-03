<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatStatus extends Model
{
    protected $table = 'riwayat_status';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanYudisium::class, 'pengajuan_id');
    }
}