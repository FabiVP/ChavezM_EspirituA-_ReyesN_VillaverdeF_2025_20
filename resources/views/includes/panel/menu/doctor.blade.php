<li class="nav-item">
    <a class="nav-link " href="/horario">
        <i class="ni ni-calendar-grid-58 text-primary"></i> Gestionar horario
    </a>
</li>
<li class="nav-item">
    <a class="nav-link " href="/miscitas">
        <i class="fas fa-clock text-info"></i> Mis citas
    </a>
</li>

 <li class="nav-item">
    <a class="nav-link" href="{{ route('medical_histories.index') }}">
        <i class="fas fa-notes-medical text-danger"></i> Antecedentes Médicos
    </a>
</li> 

<li class="nav-item">
    <a class="nav-link" href="{{ route('clinical.records.index') }}">
        <i class="fas fa-folder-open text-success"></i> Consultar expediente clínico
    </a>
</li>

{{-- 🆕 NUEVO ITEM: NOTIFICAR RESULTADOS --}}
<li class="nav-item">
    <a class="nav-link" href="{{ route('appointments.notify.results') }}">
        <i class="fas fa-file-medical text-warning"></i> Notificar Resultados Disponibles
    </a>
</li>