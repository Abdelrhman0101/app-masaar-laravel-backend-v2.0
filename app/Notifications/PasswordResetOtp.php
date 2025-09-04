<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class PasswordResetOtp extends Notification implements ShouldQueue
{
    use Queueable;

    protected $otp;
    protected $expiresAt;
    protected $userName;

    /**
     * Create a new notification instance.
     *
     * @param string $otp
     * @param Carbon $expiresAt
     * @param string $userName
     */
    public function __construct(string $otp, Carbon $expiresAt, string $userName)
    {
        $this->otp = $otp;
        $this->expiresAt = $expiresAt;
        $this->userName = $userName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $expiryMinutes = Carbon::now()->diffInMinutes($this->expiresAt);
        
        return (new MailMessage)
            ->subject('إعادة تعيين كلمة المرور - رمز التحقق')
            ->greeting('مرحباً ' . $this->userName . '!')
            ->line('لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك.')
            ->line('لإعادة تعيين كلمة المرور، يرجى استخدام رمز التحقق التالي:')
            ->line('')
            ->line('**' . $this->otp . '**')
            ->line('')
            ->line('هذا الرمز صالح لمدة ' . $expiryMinutes . ' دقائق فقط.')
            ->line('إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد الإلكتروني.')
            ->line('لا تشارك هذا الرمز مع أي شخص آخر لحماية حسابك.')
            ->action('إعادة تعيين كلمة المرور', url('/reset-password'))
            ->salutation('مع أطيب التحيات،\nفريق التطبيق');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'password_reset_otp',
            'expires_at' => $this->expiresAt->toISOString(),
            'sent_at' => Carbon::now()->toISOString(),
        ];
    }
}