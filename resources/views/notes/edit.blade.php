@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center my-4">Editar Nota</h2>

    <form action="{{ route('notes.update', $note->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">Asunto</label>
            <input type="text" name="title" class="form-control" value="{{ $note->title }}" required>
        </div>

        <div class="mb-3">
            <label for="color_id" class="form-label">Seleccionar Color</label>
            <select name="color_id" class="form-select">
                @foreach ($colors as $color)
                    <option value="{{ $color->id }}" style="background-color: {{ $color->hex }}" 
                        {{ $note->color_id == $color->id ? 'selected' : '' }}>
                        {{ $color->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Nota</button>
    </form>

    <form action="{{ route('notes.destroy', $note->id) }}" method="POST" class="mt-3">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Eliminar Nota</button>
    </form>
</div>
@endsection
