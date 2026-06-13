<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ setting('app.name') ?? config('app.name') }}</title>
</head>

<body style="margin:0;font-family:Poppins,sans-serif;background:#fff;font-size:14px;">


<div style="max-width:680px;margin:auto;padding:40px;background:#f4f7ff;">

    {{-- HEADER --}}
    <header>
        <table style="width:100%;">
            <tr>
                <td>
                    <h2>{{ setting('app.name') ?? config('app.name') }}</h2>
                </td>
                <td style="text-align:right;">
                    <span>{{ now()->format('d M, Y') }}</span>
                </td>
            </tr>
        </table>
    </header>

    {{-- BODY --}}
    <div style="margin-top:50px;background:#fff;padding:60px;text-align:center;border-radius:20px;">

        {{-- TITLE --}}
        <h1 style="color:#1f1f1f;">
            {{ setting('email.otp.title') 
                ? t(setting('email.otp.title'), $lang) 
                : __('mail.otp_title') }}
        </h1>

        {{-- MESSAGE --}}
        <p style="margin-top:20px;">
            {{ str_replace(
                ':name',
                $user->first_name . ' ' . $user->last_name,
                setting('email.otp.message')
                    ? t(setting('email.otp.message'), $lang)
                    : __('mail.otp_message')
            ) }}
        </p>

        {{-- OTP --}}
        <p style="margin-top:40px;font-size:40px;letter-spacing:10px;color:#ba3d4f;">
            {{ $otp }}
        </p>

        {{-- VALIDITY --}}
        <p style="margin-top:20px;">
            {{ __('mail.valid_for') }}
            <b>{{ setting('email.otp.validity_minutes') ?? 10 }} {{ __('mail.minutes') }}</b>
        </p>

    </div>

    {{-- FOOTER --}}
    <div style="text-align:center;margin-top:40px;color:#888;">

        <p>
            {{ __('mail.need_help') }}
            <a href="mailto:{{ setting('email.otp.footer.help_email') ?? 'support@example.com' }}">
                {{ setting('email.otp.footer.help_email') ?? 'support@example.com' }}
            </a>
        </p>

        <p>
            {{ setting('email.otp.footer.address') ?? 'Your company address' }}
        </p>

        <div style="margin-top:15px;">
            <a href="{{ setting('social.facebook') ?? '#' }}">Facebook</a>
        </div>

    </div>

</div>

</body>
</html>