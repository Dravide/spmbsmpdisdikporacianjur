<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SekolahDasar extends Model
{
    use HasFactory;

    protected $table = 'sekolah_dasar';
    protected $primaryKey = 'sekolah_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sekolah_id',
        'npsn',
        'nama',
        'kode_wilayah',
        'bentuk_pendidikan_id',
        'status_sekolah',
        'alamat_jalan',
        'desa_kelurahan',
        'rt',
        'rw',
        'lintang',
        'bujur',
    ];

    protected $casts = [
        'lintang' => 'float',
        'bujur' => 'float',
    ];

    /**
     * Get the operator (user) for this school
     */
    public function operator(): HasOne
    {
        return $this->hasOne(User::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Get students (peserta didik) associated with this school.
     */
    public function pesertaDidik()
    {
        return $this->hasMany(PesertaDidik::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Get full address
     */
    public function getAlamatLengkapAttribute(): string
    {
        $parts = array_filter([
            $this->alamat_jalan,
            $this->rt ? "RT {$this->rt}" : null,
            $this->rw ? "RW {$this->rw}" : null,
            $this->desa_kelurahan,
        ]);

        return implode(', ', $parts) ?: '-';
    }

    /**
     * Check if this school has an operator account
     */
    public function hasOperator(): bool
    {
        return $this->operator()->exists();
    }
}
