<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pendaftarans';

    protected $fillable = [
        'peserta_didik_id',
        'sekolah_menengah_pertama_id',
        'sekolah_menengah_pertama_id_2',
        'jalur_pendaftaran_id',
        'tanggal_daftar',
        'koordinat_lintang',
        'koordinat_bujur',
        'jarak_meter',
        'status',
        'nomor_pendaftaran',
        'catatan',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'koordinat_lintang' => 'double',
        'koordinat_bujur' => 'double',
        'jarak_meter' => 'float',
    ];

    public function pesertaDidik()
    {
        return $this->belongsTo(PesertaDidik::class);
    }

    public function sekolah()
    {
        return $this->belongsTo(SekolahMenengahPertama::class, 'sekolah_menengah_pertama_id', 'sekolah_id');
    }

    public function sekolah2()
    {
        return $this->belongsTo(SekolahMenengahPertama::class, 'sekolah_menengah_pertama_id_2', 'sekolah_id');
    }

    public function jalur()
    {
        return $this->belongsTo(JalurPendaftaran::class, 'jalur_pendaftaran_id');
    }

    public function berkas()
    {
        return $this->hasMany(PendaftaranBerkas::class, 'pendaftaran_id');
    }
}
