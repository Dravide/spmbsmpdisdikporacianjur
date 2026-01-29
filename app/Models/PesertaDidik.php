<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesertaDidik extends Authenticatable
{
    use HasFactory;

    protected $table = 'peserta_didiks';

    protected $fillable = [
        'password',
        'peserta_didik_id',
        'sekolah_id',
        'kode_wilayah',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nik',
        'no_kk',
        'nisn',
        'alamat_jalan',
        'desa_kelurahan',
        'rt',
        'rw',
        'nama_dusun',
        'nama_ibu_kandung',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'nama_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'nama_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'kebutuhan_khusus',
        'no_KIP',
        'no_pkh',
        'lintang',
        'bujur',
        'flag_pip',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'lintang' => 'double',
        'bujur' => 'double',
        'password' => 'hashed',
    ];

    /**
     * Role helpers for polymorphic view compatibility
     */
    public function isAdmin()
    {
        return false;
    }
    public function isOpsd()
    {
        return false;
    }
    public function isOpsmp()
    {
        return false;
    }
    public function isCmb()
    {
        return false;
    }

    /**
     * Get the school that owns the student.
     * Assuming Sekolah Dasar for now as per context, but could be generic if needed.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(SekolahDasar::class, 'sekolah_id', 'sekolah_id');
    }
}
