<form action="{{ route('acta.procesar') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <label for="pdf">Seleccione el acta de notas (PDF):</label>
    <input type="file" name="pdf" accept="application/pdf" required>
    <button type="submit">Procesar</button>
</form>
