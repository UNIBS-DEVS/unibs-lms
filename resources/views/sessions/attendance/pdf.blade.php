<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f2f2f2;
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            text-align: center;
        }

        .is_present {
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .absent {
            background-color: #dc3545;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }

        .yes {
            background-color: yellow;
            color: #000;
            font-weight: bold;
        }

        .header {
            margin-bottom: 15px;
        }

        .logo {
            float: left;
        }

        .title {
            text-align: right;
        }

        .clearfix {
            clear: both;
        }

        .footer {
            position: relative;
            top: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 11px;
            color: #555;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('assets/images/company-logo.png') }}" height="60">
        </div>

        <div class="title">
            <h3>Session Attendance Report</h3>
            <p>{{ now()->format('d M Y') }}</p>
        </div>

        <div class="clearfix"></div>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Learner</th>
                <th>Present</th>
                <th>Late Entry</th>
                <th>Early Exit</th>
                <th>Remarks</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($attendance as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <!--<td>{{ $row->learner->name }}</td>-->
                    <td>{{ $row->learner->name }}<br>{{ $row->learner->email }}</td>

                    {{-- Present --}}
                    <td
                        style="
                            background-color: {{ $row->is_present ? '#28a745' : '#dc3545' }};
                            color: #ffffff;
                            font-weight: bold;
                        ">
                        {{ $row->is_present ? 'Yes' : 'No' }}
                    </td>

                    {{-- Late Entry --}}
                    <td
                        style="
                            background-color: {{ $row->late_entry ? '#ffc107' : 'transparent' }};
                            color: #000000;
                        ">
                        {{ $row->late_entry ? 'Yes' : '-' }}
                    </td>

                    {{-- Early Exit --}}
                    <td
                        style="
                            background-color: {{ $row->early_exit ? '#ffc107' : 'transparent' }};
                            color: #000000;
                        ">
                        {{ $row->early_exit ? 'Yes' : '-' }}
                    </td>

                    {{-- Remarks --}}
                    <td>{{ $row->remarks ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>

    {{-- FOOTER --}}
    <div class="footer">
        © {{ date('Y') }} UNI Business Solutions
    </div>

</body>

</html>
