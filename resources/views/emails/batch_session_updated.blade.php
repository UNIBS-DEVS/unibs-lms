<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Session Updated</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background-color:#f5f7fa; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="500" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius:6px; overflow:hidden;">

                    @php
                        $logoPath = public_path('assets/images/company-logo.png');
                        $logoBase64 = base64_encode(file_get_contents($logoPath));
                    @endphp

                    <!-- Logo -->
                    <tr>
                        <td style="padding:20px; text-align:center; background:#f8f9fa;">
                            <img src="data:image/png;base64,{{ $logoBase64 }}" width="180" height="60"
                                alt="Unibs Logo">
                        </td>
                    </tr>

                    <!-- Header -->
                    <tr>
                        <td style="padding:20px;">
                            <h2 style="margin:0; color:#0d6efd;">
                                Session Updated: {{ $data['session_name'] }}
                            </h2>
                        </td>
                    </tr>

                    <!-- Session Details + Updated Fields (Single Column) -->
                    <tr>
                        <td style="padding:20px;">
                            <h4 style="margin-bottom:10px; color:#333;">Session Details</h4>
                            <p style="margin:4px 0;"><strong>Session:</strong> {{ $data['session_name'] }}</p>
                            <p style="margin:4px 0;"><strong>Batch:</strong> {{ $data['batch_name'] }}</p>
                            <p style="margin:4px 0;"><strong>Trainer:</strong> {{ $data['trainer_name'] ?? '—' }}</p>
                            <p style="margin:4px 0;"><strong>Course:</strong> {{ $data['course_name'] ?? '—' }}</p>

                            <h4 style="margin-top:20px; margin-bottom:10px; color:#333;">Updated Fields</h4>
                            <ul style="margin:0; padding-left:18px;">
                                @foreach ($data['changes'] as $field => $values)
                                    @php
                                        $old = $values['old'];
                                        $new = $values['new'];

                                        // Format time fields
                                        if (in_array($field, ['start_time', 'end_time'])) {
                                            if (!empty($old)) {
                                                $old = \Carbon\Carbon::parse($old)->format('h:i A');
                                            }
                                            if (!empty($new)) {
                                                $new = \Carbon\Carbon::parse($new)->format('h:i A');
                                            }
                                        }

                                        // Format date fields
                                        if (in_array($field, ['start_date', 'end_date'])) {
                                            if (!empty($old)) {
                                                $old = \Carbon\Carbon::parse($old)->format('d M Y');
                                            }
                                            if (!empty($new)) {
                                                $new = \Carbon\Carbon::parse($new)->format('d M Y');
                                            }
                                        }
                                    @endphp
                                    <li>
                                        <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                                        {{ $old }} → {{ $new }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>

                    <!-- Footer -->
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
