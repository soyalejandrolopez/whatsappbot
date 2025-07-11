@extends('layouts.admin-3d')

@section('title', 'Nueva Conversación')
@section('content')
<div class="glass-card p-4 mt-4">
    <h2 class="mb-4">Crear Nueva Conversación</h2>
    <form action="{{ route('admin.conversations.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="contact_id" class="form-label">Contacto</label>
            <select name="contact_id" id="contact_id" class="form-control-3d mb-2">
                <option value="">-- Selecciona un contacto existente --</option>
                @foreach($contacts ?? [] as $contact)
                    <option value="{{ $contact->id }}">{{ $contact->name ?? $contact->phone_number }}</option>
                @endforeach
            </select>
            <div class="text-center text-secondary mb-2">O ingresa un nuevo contacto:</div>
            <input type="text" name="new_contact_name" class="form-control-3d mb-2" placeholder="Nombre (opcional)">
            <input type="text" name="new_contact_phone" class="form-control-3d" placeholder="Teléfono (obligatorio si es nuevo)">
        </div>
        <div class="mb-3">
            <label for="priority" class="form-label">Prioridad</label>
            <select name="priority" id="priority" class="form-control-3d">
                <option value="low">Baja</option>
                <option value="medium" selected>Media</option>
                <option value="high">Alta</option>
                <option value="urgent">Urgente</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notas</label>
            <textarea name="notes" id="notes" class="form-control-3d"></textarea>
        </div>
        <button type="submit" class="btn-3d">Crear Conversación</button>
    </form>
    <div class="mt-4 text-center">
        <a href="{{ route('admin.conversations.index') }}" class="btn-3d-secondary">Ver Conversaciones</a>
    </div>
</div>
@endsection 