
<p>{{ ucwords(strtolower($user['first_name'])) }},</p>

<p>We received a request to change your password on ETOP.</p>
<p>Click the link below to set a new password</p>
<a href='{{ URL::to('reset_password/'.$token) }}'>
    {{ URL::to('reset_password/'.$token)  }}
</a>

<p>If you don't want to change your password, you can ignore this email.</p>

<br>
<p>Thanks,</p>
<p>ETOP Administrator</p>