<html>
    <form method="POST" action="/reset-password">
        @csrf
        <input type="password" name="password" />
        <input type="password-confirmation" name="password_confirmation" />
        <input type="hidden" name="token" value="{{ $token }}" />
        <input type="hidden" name="email" value="{{ $email }}" />
        <input type="submit" value="Save Passwrod" />
    </form>
</html>
