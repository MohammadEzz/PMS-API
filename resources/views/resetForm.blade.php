<html>
    <form method="POST" action="/forgot-password">
        @csrf
       email:  <input type="text" name="email" />

        <input type="submit" value="forget password" />
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    </form>
</html>
