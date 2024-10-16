<html>
    <form method="POST" action="/login">
        @csrf
        <input type="text" name="email" />
        <input type="password" name="password" />
        <input type="submit" value="login" />
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    </form>
    <a href="/resetform">Forget Password</a>

</html>
