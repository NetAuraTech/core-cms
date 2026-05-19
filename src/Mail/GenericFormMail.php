<?php

namespace Netauratech\CoreCms\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Netauratech\CoreCms\Models\Option;

class GenericFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $site_name;
    public string $logo;
    public string $appRootUrl;
    public const TYPE_CONTACT = 'contact';
    public const TYPE_GENERAL_FORM = 'general_form';

    /**
     * Create a new message instance.
     *
     * @param array $data The submitted form data (e.g., name, email, message).
     * @param string $type The form type (‘contact’ for the standard contact form, ‘general_form’ for forms with dynamic sections).
     * @param Request $request The current HTTP request (injected by the service container).
     * @param array $sections Optional data for structuring the content of general forms.
     */
    public function __construct(
        public array $data,
        public string $type,
        Request $request,
        public array $sections = [],
    ) {
        $this->loadOptions();
        $this->appRootUrl = $request->root();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $fromAddress = '';
        $fromName = '';
        $toAddress = Option::where('key', 'contact-email')->first()?->value ?: '';
        $toName = $this->site_name;
        $subject = '';
        $replyTo = [];

        $systemNoReply = Option::where('key', 'noreply-email')->first()?->value ?: config('mail.from.address');

        switch ($this->type) {
            case self::TYPE_CONTACT:
                $fromAddress = $systemNoReply;
                $fromName = trim(($this->data['lastname'] ?? '') . ' ' . ($this->data['firstname'] ?? ''));
                $subject = __('core-cms::mail.contact.request.value') . ': ' . ($this->data['subject'] ?? __('mail.no_subject')) . ' - ' . $this->site_name;
                
                $clientAddress = $this->data['email'] ?? '';
                if (!empty($clientAddress)) {
                    $replyTo = [new Address($clientAddress, $fromName)];
                }
                break;

            case self::TYPE_GENERAL_FORM:
                $fromAddress = $systemNoReply;
                $fromName = $this->site_name;
                $subject = __('core-cms::mail.form.request.value') . ' - ' . $this->site_name;
                break;
        }

        $fromAddress = $fromAddress ?: config('mail.from.address');
        $fromName = $fromName ?: $this->site_name;

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            to: [new Address($toAddress, $toName)],
            replyTo: $replyTo,
            subject: $subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $markdownTemplate = '';
        $withData = [
            'data' => $this->data,
            'sitename' => $this->site_name,
            'logo' => $this->logo,
            'url' => $this->appRootUrl,
        ];

        switch ($this->type) {
            case self::TYPE_CONTACT:
                $markdownTemplate = 'core-cms::mail.contact';
                break;

            case self::TYPE_GENERAL_FORM:
                $markdownTemplate = 'core-cms::mail.form';

                $markdownContent = "# " . __('core-cms::mail.form.request.value') . "\n\n";

                foreach ($this->sections as $section) {
                    $markdownContent .= "## " . ($section['title'] ?? 'Section') . "\n\n";

                    foreach ($section['fields'] as $field) {
                        $name = Str::slug($field['label']);
                        $label = $field['label'];
                        $type = $field['type'] ?? 'text';
                        $value = $this->data[$name] ?? null;
                        $isEmpty = is_null($value) || $value === '';

                        if ($type === 'checkbox') {
                            $checkbox = !empty($value) ? '☑' : '☐';
                            $line = $checkbox . ' ' . $label;
                        } elseif ($type === 'select' && isset($field['options'])) {
                            $lines = [];
                            foreach ($field['options'] as $option) {
                                $slugOption = Str::slug($option['option']);
                                $checked = ($slugOption === $value) ? '☑' : '☐';
                                $lines[] = $checked . ' ' . $option['option'];
                            }
                            $line = "**$label** :\n\n" . implode("\n\n", $lines);
                        } else {
                            $line = "**$label** : " . ($isEmpty ? '—' : $value);
                        }

                        $markdownContent .= $line . "\n\n";
                    }

                    $markdownContent .= "---\n\n";
                }
                $withData['markdownContent'] = $markdownContent;
                break;
        }

        return new Content(
            markdown: $markdownTemplate,
            with: $withData,
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Loads site options from the database.
     */
    protected function loadOptions(): void
    {
        $this->site_name = Option::where('key', 'site_name')->first()?->value ?? config('app.name', 'Mon Site');
        $this->logo = Option::where('key', 'logo')->first()?->value;
    }
}
