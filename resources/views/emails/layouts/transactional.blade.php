<!doctype html>
<html lang="fr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $emailTitle }}</title>
    <style>
        html, body { margin: 0 !important; padding: 0 !important; width: 100% !important; background: #f5f5f5 !important; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse !important; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        a { color: #2b909a; }
        .email-preheader { display: none !important; visibility: hidden; opacity: 0; color: transparent; height: 0; width: 0; overflow: hidden; mso-hide: all; }
        .email-shell { width: 100%; background: #f5f5f5; }
        .email-container { width: 100%; max-width: 640px; background: #ffffff; border: 1px solid #dadce0; border-radius: 12px; overflow: hidden; }
        .email-pad { padding-left: 48px; padding-right: 48px; }
        .email-title { margin: 0; color: #202124; font-family: Arial, Helvetica, sans-serif; font-size: 30px; font-weight: 700; line-height: 1.22; letter-spacing: -0.6px; }
        .email-copy { color: #5f6368; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 1.7; }
        .email-copy p { margin: 0 0 18px; }
        .email-copy strong { color: #202124; }
        .email-panel { margin: 24px 0; border: 1px solid #dadce0; border-radius: 8px; background: #ffffff; }
        .email-detail-label { width: 38%; padding: 12px 16px; border-bottom: 1px solid #eceff1; color: #70757a; font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: 700; line-height: 1.4; text-transform: uppercase; letter-spacing: .45px; vertical-align: top; }
        .email-detail-value { padding: 12px 16px; border-bottom: 1px solid #eceff1; color: #202124; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 600; line-height: 1.5; vertical-align: top; word-break: break-word; }
        .email-detail-last { border-bottom: 0 !important; }
        .email-button { display: inline-block; padding: 15px 26px; border-radius: 6px; background: #2b909a; color: #ffffff !important; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 700; line-height: 1; text-decoration: none; }
        .email-code { display: inline-block; padding: 3px 7px; border: 1px solid #dadce0; border-radius: 5px; background: #ffffff; color: #202124; font-family: Consolas, Monaco, monospace; font-size: 14px; font-weight: 700; word-break: break-all; }
        @media screen and (max-width: 680px) {
            .email-outer { padding: 16px 10px !important; }
            .email-container { border-radius: 10px !important; }
            .email-pad { padding-left: 24px !important; padding-right: 24px !important; }
            .email-title { font-size: 25px !important; }
            .email-detail-label, .email-detail-value { display: block !important; width: auto !important; padding: 10px 14px !important; }
            .email-detail-label { padding-bottom: 2px !important; border-bottom: 0 !important; }
            .email-detail-value { padding-top: 2px !important; }
            .email-button { display: block !important; text-align: center !important; }
        }
    </style>
</head>
<body>
    <div class="email-preheader">{{ $preheader ?? $emailTitle }}</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-shell">
        <tr>
            <td align="center" class="email-outer" style="padding:32px 16px">
                <!--[if mso]><table role="presentation" width="640" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="email-container">
                    <tr>
                        <td class="email-pad" style="padding-top:28px;padding-bottom:26px;border-bottom:1px solid #eceff1">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="middle">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td width="38" height="38" align="center" valign="middle" style="width:38px;height:38px">
                                                    <img src="{{ $message->embed(public_path('img/favicon.png')) }}" width="38" height="38" alt="SOLUTCLOUD" style="display:block;width:38px;height:38px;object-fit:contain">
                                                </td>
                                                <td style="padding-left:11px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:18px;font-weight:800;letter-spacing:.8px">SOLUTCLOUD</td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td align="right" valign="middle" style="color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;letter-spacing:.7px;text-transform:uppercase">{{ $emailCategory ?? 'Notification' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-pad" style="padding-top:42px;padding-bottom:44px">
                            @isset($emailBadge)
                                <div style="margin-bottom:14px;color:#2b909a;font-family:Arial,Helvetica,sans-serif;font-size:11px;font-weight:700;line-height:1.4;letter-spacing:.8px;text-transform:uppercase">{{ $emailBadge }}</div>
                            @endisset

                            <h1 class="email-title">{{ $emailTitle }}</h1>

                            @isset($emailIntro)
                                <p style="margin:14px 0 0;color:#70757a;font-family:Arial,Helvetica,sans-serif;font-size:16px;line-height:1.65">{{ $emailIntro }}</p>
                            @endisset

                            <div class="email-copy" style="margin-top:28px">
                                @yield('content')
                            </div>

                            @hasSection('action')
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top:30px">
                                    <tr>
                                        <td>@yield('action')</td>
                                    </tr>
                                </table>
                            @endif

                            @hasSection('notice')
                                <div style="margin-top:30px;padding:16px 18px;border:1px solid #dadce0;border-radius:7px;background:#f8f9fa;color:#5f6368;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:1.6">
                                    @yield('notice')
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="email-pad" style="padding-top:24px;padding-bottom:26px;border-top:1px solid #eceff1;background:#f8f9fa">
                            <p style="margin:0 0 8px;color:#3c4043;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:700">I-SOLUTIONS · SOLUTCLOUD</p>
                            <p style="margin:0 0 10px;color:#80868b;font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:1.6">Yopougon Ananeraie · 21 BP 4069 Abidjan 21 · Côte d’Ivoire</p>
                            <p style="margin:0;color:#80868b;font-family:Arial,Helvetica,sans-serif;font-size:11px;line-height:1.6">
                                <a href="https://solutcloud.com" style="color:#2b909a;text-decoration:none">solutcloud.com</a>
                                <span style="padding:0 7px;color:#bdc8ca">•</span>
                                <a href="mailto:sales@i-solutions.ci" style="color:#2b909a;text-decoration:none">sales@i-solutions.ci</a>
                            </p>
                            <p style="margin:12px 0 0;color:#a0abad;font-family:Arial,Helvetica,sans-serif;font-size:10px;line-height:1.5">© {{ now()->year }} I-SOLUTIONS. Message transactionnel envoyé par SOLUTCLOUD.</p>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
