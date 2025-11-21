@extends('layouts.panel')

@section('content')

<form action="{{ url('/horario') }}" method="POST">
    @csrf 

    <div class="card shadow">
        <div class="card-header border-0">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="mb-0">Gestionar Horario</h3>
                </div>
                <div class="col text-right">
                    <button type="submit" class="btn btn-sm btn-primary">
                        Guardar cambios
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(session('notification'))
                <div class="alert alert-success" role="alert">
                    {{ session('notification') }}
                </div>
            @endif

            @if(session('errors'))
                <div class="alert alert-danger" role="alert">
                    Los cambios se han guardado, pero se encontraron las siguientes novedades:
                    <ul>
                        @foreach(session('errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table align-items-center table-flush">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Día</th>
                        <th scope="col">Activo</th>
                        <th scope="col">Turno mañana</th>
                        <th scope="col">Turno tarde</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($horarios as $key => $horario)
                        <tr>
                            <th>{{ $days[$key] }}</th>

                            <!-- ACTIVO -->
                            <td>
                                <label class="custom-toggle">
                                    <input type="checkbox" name="active[]" value="{{ $key }}" 
                                        @if($horario->active) checked @endif>
                                    <span class="custom-toggle-slider rounded-circle"></span>
                                </label>
                            </td>

                            <!-- TURNO MAÑANA -->
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <select class="form-control" name="morning_start[]">
                                            @for ($i=8; $i<=11; $i++)
                                                <option value="{{ ($i<10?'0':'') . $i }}:00"
                                                    @if($i.':00 AM' == $horario->morning_start) selected @endif>
                                                    {{ $i }}:00 AM
                                                </option>

                                                <option value="{{ ($i<10?'0':'') . $i }}:30"
                                                    @if($i.':30 AM' == $horario->morning_start) selected @endif>
                                                    {{ $i }}:30 AM
                                                </option>
                                            @endfor
                                        </select>
                                    </div>

                                    <div class="col">
                                        <select class="form-control" name="morning_end[]">
                                            @for ($i=8; $i<=11; $i++)
                                                <option value="{{ ($i<10?'0':'') . $i }}:00"
                                                    @if($i.':00 AM' == $horario->morning_end) selected @endif>
                                                    {{ $i }}:00 AM
                                                </option>

                                                <option value="{{ ($i<10?'0':'') . $i }}:30"
                                                    @if($i.':30 AM' == $horario->morning_end) selected @endif>
                                                    {{ $i }}:30 AM
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </td>

                            <!-- TURNO TARDE CORREGIDO (2:00 PM - 6:30 PM) -->
                            <td>
                                <div class="row">
                                    <div class="col">
                                        <select class="form-control" name="afternoon_start[]">
                                            @for ($i=2; $i<=6; $i++)
                                                <option value="{{ ($i+12 < 10 ? '0' : '') . ($i+12) }}:00"
                                                    @if($i.':00 PM' == $horario->afternoon_start) selected @endif>
                                                    {{ $i }}:00 PM
                                                </option>

                                                @if($i != 6) {{-- No mostrar 6:30 PM en inicio --}}
                                                <option value="{{ ($i+12 < 10 ? '0' : '') . ($i+12) }}:30"
                                                    @if($i.':30 PM' == $horario->afternoon_start) selected @endif>
                                                    {{ $i }}:30 PM
                                                </option>
                                                @endif
                                            @endfor
                                        </select>
                                    </div>
 
                                    <div class="col">
                                        <select class="form-control" name="afternoon_end[]">
                                            @for ($i=2; $i<=6; $i++)
                                                @if($i != 2) {{-- No mostrar 2:00 PM en fin --}}
                                                <option value="{{ ($i+12 < 10 ? '0' : '') . ($i+12) }}:00"
                                                    @if($i.':00 PM' == $horario->afternoon_end) selected @endif>
                                                    {{ $i }}:00 PM
                                                </option>
                                                @endif

                                                <option value="{{ ($i+12 < 10 ? '0' : '') . ($i+12) }}:30"
                                                    @if($i.':30 PM' == $horario->afternoon_end) selected @endif>
                                                    {{ $i }}:30 PM
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</form>

@endsection