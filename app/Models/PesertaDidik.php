<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class PesertaDidik extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'kecamatan',
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
        'is_external',
        'verification_status',
        'verification_note',
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
     * Get username (NISN) for compatibility.
     */
    public function getUsernameAttribute()
    {
        return $this->nisn;
    }

    /**
     * Get role label for compatibility.
     */
    public function getRoleLabelAttribute()
    {
        return 'Calon Murid Baru';
    }

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
    /**
     * Get the school that owns the student.
     * Assuming Sekolah Dasar for now as per context, but could be generic if needed.
     */
    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(SekolahDasar::class, 'sekolah_id', 'sekolah_id');
    }

    /**
     * Get the registration for the student.
     */
    public function pendaftaran()
    {
        return $this->hasOne(Pendaftaran::class, 'peserta_didik_id', 'id')->latestOfMany();
    }

    /**
     * Check for missing mandatory fields
     */
    /**
     * Define mandatory fields for consistency
     */
    protected static function mandatoryFields()
    {
        return [
            'kecamatan' => 'Kecamatan',
        ];
    }

    /**
     * Check if there is any missing data (Fail Fast)
     */
    public function getHasMissingDataAttribute()
    {
        foreach (self::mandatoryFields() as $field => $label) {
            if (empty($this->$field)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get list of missing mandatory fields
     */
    public function getMissingDataAttribute()
    {
        $missing = [];
        foreach (self::mandatoryFields() as $field => $label) {
            if (empty($this->$field)) {
                $missing[] = $label;
            }
        }
        return $missing;
    }
}
