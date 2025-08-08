<?php

namespace Netauratech\CoreCms\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Netauratech\CoreCms\Models\Option;

class ResetPasswordNotification extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    public string $appRootUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $token)
    {
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

        return (new MailMessage)
            ->from(Option::where('key', 'noreply-email')->first()?->value ?: '', $site_name)
            ->subject(__('core-cms::mail.password.reset.value') . ' - ' . $site_name)
            ->line(__('core-cms::mail.password.reset.instruction'))
            ->action(__('core-cms::mail.password.reset.value'), url('reset-password', $this->token))
            ->line(__('core-cms::mail.password.reset.link.expire'))
            ->line(__('core-cms::mail.password.reset.no'))
            ->markdown('core-cms::mail.notification', [
                'sitename' => $site_name,
                'logo' => $logo,
                'url' => $this->appRootUrl,
            ])
            ;
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
