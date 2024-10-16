<html>
    <form method="POST" action="/register">
        @csrf
       email:  <input type="text" name="email" />
       <br/>
       name:   <input type="text" name="name" />
        <br/>
       password:   <input type="password" name="password" />
        <br/>
       cofirm:  <input type="password" name="password_confirmation" />
        <br/>
        <input type="submit" value="register" />
        {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    </form>
</html>
