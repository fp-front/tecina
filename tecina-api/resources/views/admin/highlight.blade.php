@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
          @if (session('status'))
            <div class="card">
                <div class="card-header">Aviso</div>

                <div class="card-body">
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                </div>
            </div>
          @endif
            <div class="card">
              <div class="card-header">
                <strong>Listado de destacados</strong>
                <form action="#" method="GET" style="display:inline">
                  <label>
                    <input type="text" name="filter" value="{{@$_GET['filter']}}" />
                  </label>
                  <button type="submit" class="btn btn-primary">filtrar</button>
                </form>
                <div style="float:right">
                  <a href="/api/highlights/create" class="btn btn-primary">
                    <i class="material-icons">create</i>
                    <span>Crear Destacado</span>
                  </a>
                </div>
              </div>
              <div class="card-body">
                   {{ $highlights->links('pagination::bootstrap-4') }}
                <table>
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nombre</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                  @foreach ($highlights as $highlight)
                    <tr>
                      <td>#{{$highlight->id}}</td>
                      <td>{{$highlight->name}}</td>
                      <td>
                        <a href="/api/highlights/{{$highlight->id}}/edit" class="btn btn-primary">
                          <i class="material-icons">edit</i>
                        </a>
                        <div style="float:right;margin-left:5px;">
                          <form action="/api/highlights/{{$highlight->id}}" method="POST">
                            <input type="hidden" name="_method" value="DELETE" />
                            <button type="submit" class="btn btn-danger">
                              <i class="material-icons">delete</i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
                </table>
                   {{ $highlights->links('pagination::bootstrap-4') }}
              </div>
            </div>
        </div>
    </div>
</div>
@endsection
