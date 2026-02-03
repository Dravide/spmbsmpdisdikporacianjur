<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarUlang extends Model
{
    use HasFactory;

    protected $fillable = [
        'sekolah_menengah_pertama_id',
        'pengumuman_id',
        'peserta_didik_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'keterangan',
        'status',
        'nomor_urut',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function sekolah()
    {
        return $this->belongsTo(SekolahMenengahPertama::class, 'sekolah_menengah_pertama_id', 'sekolah_id');
    }

    public function pengumuman()
    {
        return $this->belongsTo(Pengumuman::class);
    }

    public function pesertaDidik()
    {
        return $this->belongsTo(PesertaDidik::class);
    }
}
