<h2>Vista previa del acta</h2>

<form action="{{ route('acta.guardar') }}" method="POST">
    @csrf

    @foreach($cursos as $curso)
        <div style="margin-bottom: 30px; padding: 15px; border: 1px solid #ccc;">
            <h3>Curso #{{ $loop->iteration }}</h3>

            <label>ID del curso:</label>
            <input type="text" name="cursos[{{ $loop->index }}][id_curso]" required>

            <label>Mes de aprobación:</label>
            <select name="cursos[{{ $loop->index }}][mes]" required>
                @foreach(range(1,12) as $mes)
                    <option value="{{ sprintf('%02d', $mes) }}">{{ sprintf('%02d', $mes) }}</option>
                @endforeach
            </select>

            <label>Año de aprobación:</label>
            <select name="cursos[{{ $loop->index }}][anio]" required>
                @for ($a = date('Y'); $a >= 2016; $a--)
                    <option value="{{ $a }}">{{ $a }}</option>
                @endfor
            </select>

            <input type="hidden" name="cursos[{{ $loop->index }}][index]" value="{{ $loop->index }}">

            <table border="1" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 10px;">
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
                                <input type="hidden" name="cursos[{{ $loop->parent->index }}][estudiantes][{{ $idx }}][carne]" value="{{ $est['carne'] }}">
                            </td>
                            <td>
                                {{ $est['consolidado'] }}
                                <input type="hidden" name="cursos[{{ $loop->parent->index }}][estudiantes][{{ $idx }}][consolidado]" value="{{ $est['consolidado'] }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    <button type="submit">Confirmar y Guardar Todos</button>
</form>
