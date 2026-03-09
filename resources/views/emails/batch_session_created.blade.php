<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Session Scheduled</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background-color:#f5f7fa; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:6px; overflow:hidden;">

                    @php
                        $logoPath = public_path('assets/images/company-logo.png');
                        $logoBase64 = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
                    @endphp

                    {{-- Logo --}}
                    @if ($logoBase64)
                        <tr>
                            <td style="padding:20px; text-align:center; background:#f8f9fa;">
                                <img src="data:image/png;base64,{{ $logoBase64 }}" width="180" height="60"
                                    alt="Company Logo">
                            </td>
                        </tr>
                    @endif

                    {{-- Header --}}
                    <tr>
                        <td style="padding:20px;">
                            <h2 style="margin:0; color:#0d6efd;">
                                {{ $session_name }} has been scheduled for {{ $batch_name }}
                            </h2>
                        </td>
                    </tr>

                    {{-- Two Columns --}}
                    <tr>
                        <td style="padding:20px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>

                                    {{-- Column 1 : Session Details --}}
                                    <td width="50%" valign="top" style="padding-right:10px;">
                                        <h4 style="margin-bottom:10px; color:#333;">Session Details</h4>

                                        <p style="margin:4px 0;">
                                            <strong>Date:</strong><br>
                                            {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
                                            @if ($end_date != $start_date)
                                                - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                                            @endif
                                        </p>

                                        <p style="margin:4px 0;">
                                            <strong>Time:</strong><br>
                                            {{ \Carbon\Carbon::parse($start_time)->format('h:i A') }}
                                            –
                                            {{ \Carbon\Carbon::parse($end_time)->format('h:i A') }}
                                        </p>

                                        <p style="margin:4px 0;">
                                            <strong>Type:</strong> {{ $type }}
                                        </p>

                                        @if (!empty($location))
                                            <p style="margin:4px 0;">
                                                <strong>Location:</strong> {{ $location }}
                                            </p>
                                        @endif
                                    </td>

                                    {{-- Column 2 : Batch Details --}}
                                    <td width="50%" valign="top" style="padding-left:10px;">
                                        <h4 style="margin-bottom:10px; color:#333;">Batch Details</h4>

                                        <p style="margin:4px 0;">
                                            <strong>Batch:</strong><br>
                                            {{ $batch_name }}
                                        </p>

                                        <p style="margin:4px 0;">
                                            <strong>Course:</strong><br>
                                            {{ $course_name ?? '—' }}
                                        </p>

                                        <p style="margin:4px 0;">
                                            <strong>Trainer:</strong><br>
                                            {{ $trainer_name ?? '—' }}
                                        </p>
                                    </td>

                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px; background:#f8f9fa; font-size:13px; color:#555;">
                            <p style="margin:0;">
                                Thank you,<br>
                                <strong>UNIBS LMS Team</strong>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
