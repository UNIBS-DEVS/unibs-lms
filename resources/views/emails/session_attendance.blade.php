<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Session Attendance</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background-color:#f5f7fa; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" cellpadding="20" cellspacing="0" style="background:#ffffff; border-radius:6px;">

                    <!-- HEADER -->
                    <tr>
                        <td align="center" style="border-bottom:1px solid #eeeeee;">
                            <h2 style="margin:0;">Session Attendance Report</h2>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td>

                            <p>Hello,</p>

                            <p>
                                Please find attached the attendance report for the following session:
                            </p>

                            <table width="100%" cellpadding="6" cellspacing="0" border="0">

                                <tr>
                                    <td width="50%" valign="top">

                                        <table width="100%" cellpadding="4">
                                            <tr>
                                                <td><strong>Batch:</strong></td>
                                                <td>{{ $session->batch?->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Trainer:</strong></td>
                                                <td>{{ $session->trainer?->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date:</strong></td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($session->start_date)->format('d M Y') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($session->end_date)->format('d M Y') }}
                                                </td>
                                            </tr>

                                        </table>

                                    </td>

                                    <td width="50%" valign="top">

                                        <table width="100%" cellpadding="4">
                                            <tr>
                                                <td><strong>Course:</strong></td>
                                                <td>{{ $session->course?->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Session:</strong></td>
                                                <td>{{ $session->session_name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Time:</strong></td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>

                            </table>

                            <p style="margin-top:20px;">
                                📎 The attendance PDF is attached with this email.
                            </p>

                            <p>
                                Thank you!
                                <br>
                                Regards,<br>
                                <strong>{{ config('app.name') }}</strong>
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
