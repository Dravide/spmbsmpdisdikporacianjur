<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SekolahMenengahPertama extends Model
{
    use HasFactory;

    protected $table = 'sekolah_menengah_pertamas';
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
        'daya_tampung',
        'jumlah_rombel',
        'desa_kelurahan',
        'desa_kelurahan',
        'rt',
        'rw',
        'lintang',
        'bujur',
        'mode_spmb',
        'is_locked_daya_tampung',
    ];

    protected $casts = [
        'lintang' => 'float',
        'bujur' => 'float',
        'is_locked_daya_tampung' => 'boolean',
    ];

    /**
     * Get the operator (user) for this school
     */
    public function operator(): HasOne
    {
        return $this->hasOne(User::class, 'sekolah_id', 'sekolah_id')->where('role', 'opsmp');
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

    /**
     * Get the zona domisili for this school
     */
    public function zonaDomisili()
    {
        return $this->hasMany(ZonaDomisili::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Get human readable Bentuk Pendidikan
     */
    public function getBentukPendidikanDisplayAttribute()
    {
        $val = $this->bentuk_pendidikan_id;
        if ($val == 5)
            return 'SWASTA';
        if ($val == 6)
            return 'NEGERI';
        return $val;
    }
}
