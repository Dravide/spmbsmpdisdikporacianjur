<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StatusChangedNotification extends Notification
{
    use Queueable;

    protected $title;

    protected $message;

    protected $type;

    protected $actionUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $type = 'info', $actionUrl = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->actionUrl = $actionUrl;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check if user has email notifications enabled (optional)
        if (method_exists($notifiable, 'wantsEmailNotifications') && $notifiable->wantsEmailNotifications()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Halo '.($notifiable->name ?? $notifiable->nama ?? 'Peserta').'!')
            ->line($this->message);

        if ($this->actionUrl) {
            $mail->action('Lihat Detail', $this->actionUrl);
        }

        return $mail->line('Terima kasih telah menggunakan SPMB Disdikpora Cianjur.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'action_url' => $this->actionUrl,
        ];
    }

    /**
     * Helper to create registration success notification
     */
    public static function registrationSuccess($pendaftaran)
    {
        return new self(
            'Pendaftaran Berhasil',
            'Pendaftaran Anda dengan nomor '.$pendaftaran->nomor_pendaftaran.' telah berhasil dikirim.',
            'success',
            route('siswa.pendaftaran')
        );
    }

    /**
     * Helper to create verification status notification
     */
    public static function verificationStatus($pendaftaran, $status, $note = null)
    {
        $messages = [
            'verified' => 'Berkas pendaftaran Anda telah diverifikasi.',
            'rejected' => 'Berkas pendaftaran Anda ditolak. '.($note ?? 'Silakan periksa kembali.'),
        ];

        return new self(
            'Status Verifikasi: '.ucfirst($status),
            $messages[$status] ?? 'Status pendaftaran Anda telah diperbarui.',
            $status === 'verified' ? 'success' : 'warning',
            route('siswa.pendaftaran')
        );
    }

    /**
     * Helper to create announcement notification
     */
    public static function announcement($status)
    {
        $messages = [
            'diterima' => 'Selamat! Anda dinyatakan DITERIMA di sekolah pilihan Anda.',
            'ditolak' => 'Mohon maaf, Anda belum berhasil diterima di sekolah pilihan.',
        ];

        return new self(
            'Pengumuman Hasil Seleksi',
            $messages[$status] ?? 'Hasil seleksi Anda telah diumumkan.',
            $status === 'diterima' ? 'success' : 'danger',
            route('siswa.pengumuman')
        );
    }

    /**
     * Helper to create re-registration reminder
     */
    public static function reRegistrationReminder($deadline)
    {
        return new self(
            'Pengingat Daftar Ulang',
            'Jangan lupa untuk melakukan daftar ulang sebelum '.$deadline->format('d M Y H:i'),
            'warning',
            route('siswa.dashboard')
        );
    }
}
