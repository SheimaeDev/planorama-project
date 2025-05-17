@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Crear Nuevo Evento</h2>

    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="subject" class="form-label">Asunto</label>
            <input type="text" name="subject" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Fecha de Inicio</label>
            <input type="datetime-local" name="start_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">Fecha de Fin</label>
            <input type="datetime-local" name="end_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="background_color" class="form-label">Color de Fondo</label>
            <input type="color" name="background_color" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Guardar Evento</button>
    </form>
</div>
@endsection
