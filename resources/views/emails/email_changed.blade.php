<h3>Email Address Updated</h3>

<p>Hello {{ $name }},</p>

<p>Your email has been changed successfully:</p>

<p>
    Old: {{ $old_email }} <br>
    New: {{ $new_email }}
</p>

<p>
    Login here:
    <a href="{{ route('login') }}">Login</a>
</p>

<p>If this was not you, contact support immediately.</p>

<p>Thank you!</p>
<p>Regards,<br>UNIBS LMS Team</p>
