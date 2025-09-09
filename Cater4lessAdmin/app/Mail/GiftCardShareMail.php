<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\EmailTemplate;

class GiftCardShareMail extends Mailable
{
   use Queueable, SerializesModels;

    protected $acceptUrl;
    protected $toName;
    protected $fromName;

    public function __construct($acceptUrl, $toName, $fromName = null)
    {
        $this->acceptUrl = $acceptUrl;
        $this->toName = $toName;
        $this->fromName = $fromName;
    }

    public function build()
{
    $company_name = BusinessSetting::where('key', 'business_name')->first()->value ?? config('app.name');

    $data = EmailTemplate::where('type','user')
        ->where('email_type', 'gift_card_share')
        ->first();

    $template = $data ? (int)$data->email_template : 1;

    // Values to replace
    $user_name = $this->toName ?? '';
    $from_name = $this->fromName ?? 'Someone';
    $link = $this->acceptUrl ?? '';
    // If you have a share model with message, pass it in constructor and use it; otherwise fallback
    $message = $data->message ?? ($this->share->message ?? '') ?? '';

    // Get raw body from template (handle model or array)
    $rawBody = $data ? ($data->body ?? '') : 'You received a gift card from {{from_name}}. Click link to accept: {{link}}';

    // Replacement map (you can add more tokens if needed)
    $replacements = [
        '{{user_name}}' => $user_name,
        '{{from_name}}' => $from_name,
        '{{link}}'      => $link,
        '{{message}}'   => $message,
        // also support tokens without braces if your helper used them:
        '{user_name}' => $user_name,
        '{from_name}' => $from_name,
        '{link}'      => $link,
        '{message}'   => $message,
    ];

    // Replace tokens in the raw body and other template fields
    $body = str_replace(array_keys($replacements), array_values($replacements), $rawBody);
    $title = $data ? str_replace(array_keys($replacements), array_values($replacements), ($data->title ?? 'You have received a gift card')) : 'You have received a gift card';
    $footer_text = $data ? str_replace(array_keys($replacements), array_values($replacements), ($data->footer_text ?? '')) : '';
    $copyright_text = $data ? str_replace(array_keys($replacements), array_values($replacements), ($data->copyright_text ?? '')) : '';

    return $this->subject(translate('Gift_Card_Share_Mail'))
        ->view('email-templates.new-email-format-'.$template, [
            'company_name' => $company_name,
            'data' => $data,
            'title' => $title,
            'body' => $body,
            'footer_text' => $footer_text,
            'copyright_text' => $copyright_text,
            'link' => $link
        ]);
}

}
