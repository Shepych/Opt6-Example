@extends('layouts.main')

@section('css')
    html {
        height: 100%;
        min-height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
@endsection

@section('content')
    <form class="form-group" action="{{ route('login') }}" method="post" style="width: 500px">
        <h1 class="text-center">Авторизация</h1>
        @csrf
        <input class="form-control" type="text" name="email" placeholder="E-Mail"><br>
        <input class="form-control" type="password" name="password" placeholder="Пароль"><br>
        <input style="width: 200px;margin: 0 auto" class="btn btn-primary d-block" type="submit" value="Войти">
    </form>
@endsection

{{--<h1>Регистрация</h1>--}}
{{--<form action="{{ route('register') }}" method="post">--}}
{{--    @csrf--}}
{{--    <input type="text" name="name" placeholder="name"><br>--}}
{{--    <input type="text" name="email" placeholder="email"><br>--}}
{{--    <input type="password" name="password" placeholder="password 1"><br>--}}
{{--    <input type="password" name="password_confirmation" placeholder="password 2"><br>--}}
{{--    <input type="submit" value="Войти">--}}
{{--</form>--}}

