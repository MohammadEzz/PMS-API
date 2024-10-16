<html>
@if ($user)
<span>you are login if you want to logout click on this link <a>logout</a></span>
<form method="POST" action="/logout">
    @csrf
    <input type="submit" value="Logout">
</form>
@endif
<body>
    <h1>hello {{ $user->name }} to my dashboard</h1>
    <div>
        I'll send message to your email: {{$user->email}}
    </div>
</body>
</html>
