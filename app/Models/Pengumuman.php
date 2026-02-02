<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';

    protected $fillable = [
        'sekolah_menengah_pertama_id',
        'jalur_pendaftaran_id',
        'pendaftaran_id',
        'peserta_didik_id',
        'status',
        'keterangan',
    ];

    public function sekolah()
    {
        return $this->belongsTo(SekolahMenengahPertama::class, 'sekolah_menengah_pertama_id');
    }

    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_pendaftaran_id');
    }

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pesertaDidik()
    {
        return $this->belongsTo(PesertaDidik::class);
    }
}
