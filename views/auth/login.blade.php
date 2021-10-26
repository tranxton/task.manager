@extends('index')
@section('page')
    <main>
        <div class="row g-5 mt-5">
            <div class="col-8 offset-2">
                <h4 class="mb-3">Авторизация</h4>
                <form class="needs-validation" method="post" novalidate="">
                    <div class="col-12">
                        <div class="input-group has-validation">
                            <span class="input-group-text">Логин</span>
                            <input type="text" class="form-control @isset($errors['message'])is-invalid @endisset"
                                   name="login" id="login"
                                   @isset($errors['fields']['login'])
                                   value="{{ $errors['fields']['login'] }}"
                                   @endisset
                                   required="">
                            <div class="invalid-feedback">
                                @isset($errors['message'])
                                    {{ $errors['message'] }}
                                @endisset
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-1">
                        <div class="input-group has-validation">
                            <span class="input-group-text">Пароль</span>
                            <input type="password" class="form-control" name="password" id="password" required="">
                        </div>
                    </div>
                    <button class="w-100 btn btn-primary btn-lg mt-1" type="submit">Войти</button>
                </form>
            </div>
        </div>
    </main>
@endsection