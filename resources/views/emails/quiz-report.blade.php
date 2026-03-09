<!DOCTYPE html>
<html>

<body>

    <p>Hello {{ $user->name }},</p>

    <p>Your requested quiz report is attached.</p>

    <p>
        Regards,<br>
        {{ config('app.name') }}
    </p>

</body>

</html>
