@extends('layouts.app')

@section('content')

<style>
    .bg-orange {
        background-color: #fd7e14;
        color: #000;
    }

    .ruta-card {
        border-left: 5px solid #0d6efd;
        background: #f8f9fa;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 6px;
    }
</style>

@php
    function badgeNivel($nivel) {
        return match($nivel) {
            'Baja' => '<span class="badge bg-success">🟢 Baja</span>',
            'Media' => '<span class="badge bg-warning text-dark">🟡 Media</span>',
            'Alta' => '<span class="badge bg-orange">🟠 Alta</span>',
            'Saturada' => '<span class="badge bg-danger">🔴 Saturada</span>',
            default => '<span class="badge bg-secondary">'.$nivel.'</span>',
        };
    }
@endphp

<h2>Análisis del Parque</h2>

@if(session('success'))
    <div class="alert alert-success mt-3">{{ session('success') }}</div>
@endif

<div class="alert alert-info mt-3">
    La ruta óptima se genera ordenando las atracciones por menor porcentaje de ocupación.
    Primero se sugieren las atracciones con menor saturación.
</div>

<div class="card shadow mt-3">
    <div class="card-header">Generar análisis</div>
    <div class="card-body">
        <form action="{{ route('analisis.generar') }}" method="POST">
            @csrf

            <label class="form-label">Registro de visita</label>
            <select name="visita_id" class="form-control mb-3">
                <option value="">Selecciona una visita</option>
                @foreach($visitas as $visita)
                    <option value="{{ $visita->id }}">
                        {{ $visita->atraccion->nombre }} |
                        {{ $visita->fecha }} |
                        Visitantes: {{ $visita->cantidad_visitantes }}
                    </option>
                @endforeach
            </select>

            <button class="btn btn-primary">Generar análisis</button>
        </form>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header">Filtros</div>
    <div class="card-body">
        <form method="GET" action="{{ route('analisis.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label>Atracción</label>
                    <select name="atraccion_id" class="form-control">
                        <option value="">Todas</option>
                        @foreach($atracciones as $atraccion)
                            <option value="{{ $atraccion->id }}">
                                {{ $atraccion->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Fecha fin</label>
                    <input type="date" name="fecha_fin" class="form-control">
                </div>

                <div class="col-md-3">
                    <label>Nivel</label>
                    <select name="nivel_demanda" class="form-control">
                        <option value="">Todos</option>
                        <option value="Baja">🟢 Baja</option>
                        <option value="Media">🟡 Media</option>
                        <option value="Alta">🟠 Alta</option>
                        <option value="Saturada">🔴 Saturada</option>
                    </select>
                </div>
            </div>

            <button class="btn btn-secondary mt-3">Filtrar</button>
            <a href="{{ route('analisis.index') }}" class="btn btn-outline-secondary mt-3">Limpiar</a>
        </form>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header">Ruta óptima sugerida</div>
    <div class="card-body">
        @if($rutaOptima->count() > 0)
            @foreach($rutaOptima as $index => $ruta)
                <div class="ruta-card">
                    <strong>{{ $index + 1 }}. {{ $ruta->atraccion->nombre }}</strong>
                    <br>
                    Ocupación:
                    <strong>{{ number_format($ruta->porcentaje_ocupacion, 2) }}%</strong>
                    <br>
                    Nivel:
                    {!! badgeNivel($ruta->nivel_demanda) !!}
                </div>
            @endforeach
        @else
            <p class="text-muted">Todavía no hay datos para calcular ruta óptima.</p>
        @endif
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header">Resultados</div>
    <div class="card-body">
        @if($analisis->count() > 0)
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Atracción</th>
                        <th>Visitantes</th>
                        <th>Capacidad</th>
                        <th>Ocupación</th>
                        <th>Nivel</th>
                        <th>Recomendación</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($analisis as $item)
                    <tr>
                        <td>{{ $item->atraccion->nombre }}</td>
                        <td>{{ $item->visitantes_registrados }}</td>
                        <td>{{ $item->capacidad_maxima }}</td>
                        <td>
                            <strong>{{ number_format($item->porcentaje_ocupacion, 2) }}%</strong>
                        </td>
                        <td>
                            {!! badgeNivel($item->nivel_demanda) !!}
                        </td>
                        <td>{{ $item->recomendacion }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">Todavía no hay análisis generados.</p>
        @endif
    </div>
</div>

@endsection