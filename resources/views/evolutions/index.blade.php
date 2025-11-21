@extends('layouts.panel')

@section('content')
<div class="card shadow">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">Evoluciones médicas del expediente</h3>
        <a href="{{ route('medical_histories.show', $medicalHistory->id) }}" class="btn btn-sm btn-success">
            <i class="fas fa-chevron-left"></i> Regresar
        </a>
    </div>

    <div class="card-body">

        {{-- Mensajes de éxito --}}
        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Botón para registrar evolución (solo doctor) --}}
        @if(Auth::user()->role === 'doctor')
            <div class="text-right mb-3">
                <a href="{{ route('evolutions.create', $medicalHistory->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Registrar evolución
                </a>
            </div>
        @endif

        {{-- Listado de evoluciones --}}
        @if($evolutions->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i> No se han registrado evoluciones médicas aún.
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-items-center table-flush">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Médico</th>
                            <th scope="col">Diagnóstico</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evolutions as $index => $evolution)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{-- ✅ CORREGIDO: Convertir string a Carbon --}}
                                    {{ \Carbon\Carbon::parse($evolution->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td>
                                    {{-- ✅ CORREGIDO: Usar doctor_name del SP --}}
                                    {{ $evolution->doctor_name ?? 'N/A' }}
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($evolution->diagnosis, 50) }}</td>
                                <td>
                                    <a href="{{ route('evolutions.show', $evolution->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if(Auth::user()->role === 'doctor' && Auth::id() === $evolution->doctor_id)
                                        <a href="{{ route('evolutions.edit', $evolution->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection