<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanDokumen extends Model
{
    protected $table = 'pengajuan_dokumen';
    protected $guarded = ['id'];

    public function pengajuan()
    {
        return $this->belongsTo(PengajuanYudisium::class, 'pengajuan_id');
    }

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class, 'jenis_dokumen_id');
    }
}