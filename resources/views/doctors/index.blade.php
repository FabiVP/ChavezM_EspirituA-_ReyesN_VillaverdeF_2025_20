@extends('layouts.panel')

@section('content')

<div class="card shadow">
    <div class="card-header border-0">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="mb-0">Médicos</h3>
            </div>
            <div class="col text-right">
                <a href="{{ url('/medicos/create') }}" class="btn btn-sm btn-primary">Nuevo médico</a>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if(session('notification'))
            <div class="alert alert-success" role="alert">
                {{ session('notification') }}
            </div>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table align-items-center table-flush">
            <thead class="thead-light">
                <tr>
                    <th scope="col">Nombre</th>
                    <th scope="col">Correo</th>
                    <th scope="col">DNI</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>

            <tbody>
                @foreach($doctors as $doctor)
                <tr>
                    <th scope="row">{{ $doctor->name }}</th>
                    <td>{{ $doctor->email }}</td>
                    <td>{{ $doctor->cedula }}</td>

                    <td>
                        <form action="{{ url('/medicos/'.$doctor->id) }}" method="POST">
                            <a href="{{ url('/medicos/'.$doctor->id.'/edit') }}" class="btn btn-sm btn-primary">
                                Editar
                            </a>

                            {{-- Protección de usuarios fijos --}}
                            @if ($doctor->id != 1 && $doctor->id != 2)
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            @else
                                <span class="badge badge-secondary">Protegido</span>
                            @endif
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
    