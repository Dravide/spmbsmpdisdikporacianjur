<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $fillable = [
        'label',
        'keyword',
        'tanggal_mulai',
        'tanggal_selesai',
        'aktif',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'aktif' => 'boolean',
    ];

    /**
     * Check if a schedule is currently open.
     */
    public static function isOpen($keyword)
    {
        $now = now();
        $jadwal = self::where('keyword', $keyword)->first();

        // If schedule doesn't exist, assume it's open (or closed depending on policy, typically safe to assume closed for strict features)
        // Let's assume CLOSED if not defined, to force admin configuration.
        if (!$jadwal) {
            return false;
        }

        if (!$jadwal->aktif) {
            return false;
        }

        return $now->between($jadwal->tanggal_mulai, $jadwal->tanggal_selesai);
    }

    /**
     * Get the message for a schedule (e.g. why it's closed).
     */
    public static function getMessage($keyword)
    {
        $jadwal = self::where('keyword', $keyword)->first();

        if (!$jadwal) {
            return 'Jadwal belum ditentukan.';
        }

        if ($jadwal->deskripsi) {
            return $jadwal->deskripsi;
        }

        $now = now();
        if ($now->lessThan($jadwal->tanggal_mulai)) {
            return 'Jadwal belum dibuka. Mulai tanggal ' . $jadwal->tanggal_mulai->translatedFormat('d F Y H:i');
        }

        if ($now->greaterThan($jadwal->tanggal_selesai)) {
            return 'Jadwal sudah ditutup pada tanggal ' . $jadwal->tanggal_selesai->translatedFormat('d F Y H:i');
        }

        return 'Sedang berlangsung.';
    }
}
