@extends('index')
@section('page')
    <div class="row">
        <main class="col-12 mt-5">
            <div class="row">
                @foreach($messages as $message)
                    <div class="alert alert-{{ $message['type'] }} alert-dismissible fade show" role="alert">
                        {{ $message['message'] }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endforeach
                <div class="col-4">
                    <h2>Список задач</h2>
                </div>
                <div class="col-2 offset-6">
                    <a href="/task" type="button" class="btn btn-dark">Создать</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Название</th>
                        <th scope="col">Описание</th>
                        <th scope="col">Email</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->id }}</td>
                            <td>{{ $task->name }} @if($task->is_done)<span
                                        class="badge bg-info text-dark">Выполнена</span>@endif</td>
                            <td>{{ $task->description }}</td>
                            <td>{{ $task->email }}</td>
                            <td>
                                @if(isset($user) && !$task->is_done)
                                    <a href="/task?id={{ $task->id }}" type="button" class="btn btn-primary">Редактировать</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <form id="page_form" action="/" method="GET" class="col-3">
                    <input id="page_pagination" onchange="this.form.submit()" type="hidden" name="page" value="{{ $pagination['current'] }}">
                    <fieldset>
                        <div class="mb-3">
                            <label for="disabledSelect" class="form-label">Сортировка</label>
                            <select id="disabledSelect" class="form-select" name="sort" onchange="this.form.submit()">
                                @foreach($sorting['list'] as $db_field_name => $field)
                                    @foreach($field['types'] as $db_sort_type => $sort_type)
                                        <option value="{{ $db_field_name }}:{{$db_sort_type}}"
                                                @if($db_field_name === $sorting['selected']['field'] && $db_sort_type === $sorting['selected']['type'])
                                                selected
                                                @endif >
                                            {{ $field['name'] }}({{ $sort_type }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </fieldset>
                </form>
                <nav class="col-3 offset-1" aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item">
                            <a class="btn page-link @if(!isset($pagination['prev']))disabled @endif"
                               @if(isset($pagination['prev']))onclick="setPage('{{$pagination['prev']}}')"@endif>Предыдущая</a>
                        </li>
                        <li class="page-item">
                            <a class="btn page-link @if(!isset($pagination['next']))disabled @endif"
                               @if(isset($pagination['next']))onclick="setPage('{{$pagination['next']}}')"@endif>Следующая</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </main>
    </div>
    <script>
        function setPage(value)
        {
          document.getElementById('page_pagination').value = value;
          document.getElementById('page_form').submit();
        }
    </script>
@endsection