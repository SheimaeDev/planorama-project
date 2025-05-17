@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Crear Nota</h2>

    <form action="{{ route('notes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Asunto</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="color_id" class="form-label">Seleccionar Color</label>
            <select name="color_id" class="form-select">
                @foreach ($colors as $color)
                    <option value="{{ $color->id }}" style="background-color: {{ $color->hex }}">
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar Nota</button>
        <a href="{{ route('notes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
