<h3>Welcome {{ $name }}</h3>

<p>Your account has been created successfully.</p>

<p><strong>Login Details:</strong></p>
<p>
    Email: {{ $email }} <br>
    Password: {{ $password }}
</p>

<p>
    Login here:
    <a href="{{ route('login') }}">Login</a>
</p>

<p>Please change your password after login.</p>

<p>Thank you!</p>
<p>Regards,<br>UNIBS LMS Team</p>
