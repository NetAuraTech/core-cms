<?php

namespace Netauratech\CoreCms\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Netauratech\CoreCms\Models\Option;

class EmailVerificationNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public mixed $user;

    public string $appRootUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($user = '')
    {
        $this->user = $user ?: Auth::user();         //if user is not supplied, get from session
        $this->appRootUrl = request()->root();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $site_name = Option::where('key', 'site_name')->first()?->value;
        $logo = Option::where('key', 'logo')->first()?->value;

        URL::formatHostUsing(function () {
            return $this->appRootUrl;
        });

        $actionUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->from(Option::where('key', 'noreply-email')->first()?->value ?: '', $site_name)
            ->subject(__('core-cms::mail.email.verify.value') . ' - ' . $site_name)
            ->line(__('core-cms::mail.email.verify.instruction'))
            ->action(__('core-cms::mail.email.verify.value'), $actionUrl)
            ->markdown('core-cms::notifications.email', [
                'sitename' => $site_name,
                'logo' => $logo,
                'url' => $this->appRootUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
