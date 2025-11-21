<h2>Vista previa del acta</h2>

<form action="{{ route('acta.guardar') }}" method="POST">
    @csrf

    @php
        use App\Models\Curso;
        $idCarrera = session('carrera_seleccionada');
        $ciclo = session('ciclo_seleccionado');
        $sede = session('sede_seleccionada');
        $cursosCarrera = Curso::where('id_carrera', $idCarrera)
                            ->where('ciclo', $ciclo)
                            ->get();
    @endphp

    <div style="text-align: right;">
        <a href="{{ url('/acta/subir') }}" class="btn-regresar">← Regresar</a>
    </div>

    @foreach($cursos as $curso)
        <div style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc;">
            <h3>Curso #{{ $loop->iteration }}</h3>

            <div class="curso-header">
                <div class="campo">
                    <label>Seleccione el curso:</label>
                    <select name="cursos[{{ $loop->index }}][id_curso]" required>
                        <option value="">-- Seleccione un curso --</option>
                        @foreach ($cursosCarrera as $c)
                            <option value="{{ $c->id_curso }}">{{ $c->nombre }} ({{ $c->id_curso }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="campo">
                    <label>Sede:</label>
                    <p><strong>{{ $sede }}</strong></p>
                    <input type="hidden" name="cursos[{{ $loop->index }}][id_sede]" value="{{ $sede }}">
                </div>

                <div class="campo">
                    <label>Mes de aprobación:</label>
                    <select name="cursos[{{ $loop->index }}][mes]" required>
                        @foreach(range(1,12) as $mes)
                            <option value="{{ sprintf('%02d', $mes) }}">{{ sprintf('%02d', $mes) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="campo">
                    <label>Año de aprobación:</label>
                    <select name="cursos[{{ $loop->index }}][anio]" required>
                        @for ($a = date('Y'); $a >= 2016; $a--)
                            <option value="{{ $a }}">{{ $a }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <input type="hidden" name="cursos[{{ $loop->index }}][index]" value="{{ $loop->index }}">

            <table class="tabla-notas" border="1" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 10px;">
                <thead>
                    <tr>
                        <th>Carné</th>
                        <th>Consolidado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($curso['estudiantes'] as $idx => $est)
                        <tr>
                            <td>
                                {{ $est['carne'] }}
                                <input type="hidden"
                                    name="cursos[{{ $loop->parent->index }}][estudiantes][{{ $idx }}][carne]"
                                    value="{{ $est['carne'] }}">
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="cursos[{{ $loop->parent->index }}][estudiantes][{{ $idx }}][consolidado]"
                                    value="{{ $est['consolidado'] }}"
                                    min="0"
                                    max="100"
                                    class="nota-input">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <button type="submit">Confirmar y Guardar Todos</button>
</form>

<style>
    .tabla-notas {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14px;
        background-color: #f9f9f9;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    .tabla-notas thead {
        background-color: #007bff;
        color: white;
    }

    .tabla-notas th,
    .tabla-notas td {
        padding: 8px 12px;
        border: 1px solid #ccc;
        text-align: center;
    }

    .tabla-notas tr:nth-child(even) {
        background-color: #f1f1f1;
    }

    .tabla-notas tr:hover {
        background-color: #e0e0e0;
    }

    form {
        margin: 30px;
        font-family: Arial, sans-serif;
    }

    label {
        display: block;
        margin-top: 10px;
        font-weight: bold;
    }

    input[type="text"],
    select {
        padding: 6px;
        width: 250px;
        margin-bottom: 10px;
        display: block;
    }

    button {
        margin-top: 20px;
        padding: 10px 20px;
        font-size: 16px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    .curso-header {
    display: flex;
    gap: 20px;
    align-items: flex-end;
    flex-wrap: wrap;
    margin-top: 10px;
    margin-bottom: 10px;
    }

    .campo {
        display: flex;
        flex-direction: column;
        min-width: 200px;
    }

    .btn-regresar {
        display: inline-block;
        margin-bottom: 20px;
        padding: 8px 16px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
    }

    .btn-regresar:hover {
        background-color: #5a6268;
    }

    .nota-input {
        width: 80px;
        padding: 4px 6px;
        border: 1px solid #bfc3c9;
        border-radius: 4px;
        text-align: center;
        font-size: 13px;
        background-color: #ffffff;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.05);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }

    .nota-input::-webkit-outer-spin-button,
    .nota-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .nota-input[type="number"] {
        -moz-appearance: textfield;
    }

    .nota-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        background-color: #fdfefe;
    }
</style>
