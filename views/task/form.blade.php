@extends('index')
@section('page')
    <main>
        <div class="row g-5 mt-5">
            <div class="col-8 offset-2">
                <h4 class="mb-3">{{ $title }}</h4>
                @isset($errors['message'])
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors['message'] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endisset
                <form action="{{ $url }}" class="needs-validation" method="post" novalidate="">
                    @isset($task)
                        <input type="hidden" name="id" value="{{ $task->id }}">
                    @endisset
                    <div class="mb-3">
                        <label for="name" class="form-label">Название</label>
                        <input type="email" class="form-control @isset($errors['name'])is-invalid @endisset "
                               name="name" id="name"
                               aria-describedby="emailHelp" @isset($task)value="{{ $task->name }}"
                               disabled @endisset
                        >
                        <div class="invalid-feedback">
                            @isset($errors['name'])
                                @foreach($errors['name'] as $error)
                                    {{ $error }}
                                @endforeach
                            @endisset
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" class="form-control @isset($errors['email'])is-invalid @endisset"
                               name="email" id="email"
                               @isset($task)value="{{ $task->email }}"
                               disabled @endisset>
                        <div class="invalid-feedback">
                            @isset($errors['email'])
                                @foreach($errors['email'] as $error)
                                    {{ $error }}
                                @endforeach
                            @endisset
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Описание</label>
                        <textarea class="form-control @isset($errors['description'])is-invalid @endisset"
                                  placeholder="Текст описания" name="description"
                                  id="description"
                                  style="height: 100px">@isset($task){{ $task->description }}@endisset</textarea>
                        <div class="invalid-feedback">
                            @isset($errors['description'])
                                @foreach($errors['description'] as $error)
                                    {{ $error }}
                                @endforeach
                            @endisset
                        </div>
                    </div>
                    @isset($task)
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="is_done" id="is_done">
                            <label class="form-check-label @isset($errors['is_done'])is-invalid @endisset"
                                   for="is_done">Отметить выполненной</label>
                            <div class="invalid-feedback">
                                @isset($errors['is_done'])
                                    @foreach($errors['is_done'] as $error)
                                        {{ $error }}
                                    @endforeach
                                @endisset
                            </div>
                        </div>
                    @endisset
                    <button type="submit" class="btn btn-primary">{{ $button }}</button>
                </form>
            </div>
        </div>
    </main>
@endsection