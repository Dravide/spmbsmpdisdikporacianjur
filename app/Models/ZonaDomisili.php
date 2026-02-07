<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZonaDomisili extends Model
{
    use HasFactory;

    protected $fillable = [
        'sekolah_id',
        'kecamatan',
        'desa',
        'rw',
        'rt',
    ];

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(SekolahMenengahPertama::class, 'sekolah_id', 'sekolah_id');
    }
}
