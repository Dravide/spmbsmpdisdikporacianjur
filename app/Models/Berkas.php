<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;

    protected $table = 'berkas';

    protected $fillable = [
        'nama',
        'deskripsi',
        'jenis',
        'is_required',
        'max_size_kb',
        'form_fields',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'form_fields' => 'array',
    ];

    public function jalurPendaftarans()
    {
        return $this->belongsToMany(JalurPendaftaran::class, 'berkas_jalur_pendaftaran', 'berkas_id', 'jalur_pendaftaran_id');
    }
}
