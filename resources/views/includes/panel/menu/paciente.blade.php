<li class="nav-item">
    <a class="nav-link " href="/reservarcitas/create">
        <i class="ni ni-calendar-grid-58 text-primary"></i> Reservar cita
    </a>
</li>
<li class="nav-item">
    <a class="nav-link " href="/miscitas">
        <i class="fas fa-clock text-info"></i> Mis citas
    </a>
</li>

<li class="nav-item">
    <a class="nav-link" href="{{ route('clinical.record.show', auth()->id()) }}">
        <i class="fas fa-folder-open text-success"></i> Mi expediente clínico
    </a>
</li>
