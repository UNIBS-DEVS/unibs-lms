<h3>Password Updated</h3>

<p>Hello {{ $name }},</p>

<p>Your password was successfully changed.</p>

<p>
    New Password: {{ $new_password }}
</p>

<p>
    Login here:
    <a href="{{ route('login') }}">Login</a>
</p>

<p>If this was not you, contact support immediately.</p>

<p>Thank you!</p>
<p>Regards,<br>UNIBS LMS Team</p>
