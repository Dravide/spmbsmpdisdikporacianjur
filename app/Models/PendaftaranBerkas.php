<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendaftaranBerkas extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran_berkases';

    protected $fillable = [
        'pendaftaran_id',
        'berkas_id',
        'file_path',
        'nama_file_asli',
        'form_data',
    ];

    protected $casts = [
        'form_data' => 'array',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function berkasRef()
    {
        return $this->belongsTo(Berkas::class, 'berkas_id');
    }

    public function berkas()
    {
        return $this->belongsTo(Berkas::class, 'berkas_id');
    }
}
