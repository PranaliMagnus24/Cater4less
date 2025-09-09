<!DOCTYPE html>
@php
    $lang = \App\CentralLogics\Helpers::system_default_language();
    $site_direction = \App\CentralLogics\Helpers::system_default_direction();
@endphp
<html lang="{{ $lang }}" class="{{ $site_direction === 'rtl' ? 'active' : '' }}">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ $title ?? translate('Email_Template') }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;1,400&display=swap');
        body{margin:0;font-family:Roboto, sans-serif;font-size:13px;line-height:21px;color:#737883;background:#e9ecef;padding:0;display:flex;align-items:center;justify-content:center;min-height:100vh;}
        h1,h2,h3,h4,h5,h6{color:#334257;margin:0 0 10px 0}
        .main-table{width:100%;max-width:600px;background:#fff;margin:16px;padding:28px;box-sizing:border-box;border-radius:6px;overflow:hidden}
        .text-center{text-align:center}
        .mail-img-2{width:140px;height:60px;object-fit:contain;margin:0 auto 10px;display:block}
        .cmn-btn{background:#ffa726;color:#fff;padding:10px 18px;display:inline-block;text-decoration:none;border-radius:4px}
        .mb-1{margin-bottom:5px}.mb-2{margin-bottom:10px}.mb-3{margin-bottom:15px}.mb-4{margin-bottom:20px}
        .footer{border-top:1px solid rgba(0,0,0,0.06);padding-top:14px;margin-top:20px;color:#8a8f96;font-size:12px;text-align:center}
        a{color:#334257}
        .body-html{color:#4b5563}
        .preheader{display:none!important;visibility:hidden;opacity:0;height:0;width:0}
    </style>
</head>
<body style="background-color:#e9ecef;padding:15px">
    <table role="presentation" class="main-table" dir="{{ $site_direction }}">
        <tr>
            <td>
                {{-- optional icon/logo --}}
                <div class="text-center mb-3">
                    <img class="mail-img-2"
                         src="{{ $data['icon_full_url'] ?? dynamicAsset('/public/assets/admin/img/blank1.png') }}"
                         alt="{{ $company_name ?? 'Company' }}" />
                </div>

                {{-- title --}}
                <div class="text-center mb-3">
                    <h2 id="mail-title">{{ $title ?? translate('You_have_received_a_gift_card') }}</h2>
                </div>

                {{-- main body (may contain HTML) --}}
                <div id="mail-body" class="body-html mb-3">
                    {!! $body ?? ('Hello, You have received a gift card. Click the button below to accept it.') !!}
                </div>

                {{-- optional CTA/button --}}
                @if(!empty($link))
                    <div class="text-center mb-3">
                        <a href="{{ $link }}" class="cmn-btn" id="mail-button" target="_blank" rel="noopener noreferrer">
                            {{ $data['button_name'] ?? translate('Accept_Gift_Card') }}
                        </a>
                    </div>
                @endif

                {{-- fallback link displayed as text for email clients that block buttons --}}
                @if(!empty($link))
                    <div class="mb-3" style="word-break:break-all;color:#6b7280;font-size:13px;text-align:center">
                        <small>{{ translate('Or_copy_and_paste_this_link_in_your_browser') }}:</small>
                        <div style="margin-top:6px"><a href="{{ $link }}" target="_blank" rel="noopener noreferrer">{{ $link }}</a></div>
                    </div>
                @endif

                {{-- message/footer area --}}
                <div class="footer">
                    <div id="mail-footer" class="mb-2">
                        {!! $footer_text ?? translate('If_you_have_any_questions_contact_us') !!}
                    </div>

                    <div style="margin-top:6px;">
                        {{ translate('Thanks_&_Regards') }},<br>
                        <strong>{{ $company_name ?? config('app.name') }}</strong>
                    </div>

                    <div style="margin-top:10px;color:#9ca3af;font-size:12px">
                        {!! $copyright_text ?? '&copy; ' . date('Y') . ' ' . ($company_name ?? config('app.name')) !!}
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
