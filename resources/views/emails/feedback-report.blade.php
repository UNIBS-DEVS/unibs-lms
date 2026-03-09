<!DOCTYPE html>
<html>

<body>
    <p>Hello {{ $user->name }},</p>

    <p>
        Please find attached the feedback report.
    </p>

    <p>
        Regards,<br>
        {{ config('app.name') }}
    </p>
</body>

</html>