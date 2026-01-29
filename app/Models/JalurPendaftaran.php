<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JalurPendaftaran extends Model
{
    use HasFactory;

    protected $table = 'jalur_pendaftarans';

    protected $fillable = [
        'nama',
        'deskripsi',
        'aktif',
        'kuota',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'aktif' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function berkas()
    {
        return $this->belongsToMany(Berkas::class, 'berkas_jalur_pendaftaran', 'jalur_pendaftaran_id', 'berkas_id');
    }
}
