<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Subir Acta de Notas</title> 
</head>
<body>

    <div class="form-container">
    
        <h2>Subir Acta Escaneada</h2>

        @if(session('success'))
            <div class="alert">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('acta.procesar') }}" enctype="multipart/form-data">
            @csrf

            <label for="carrera">Carrera:</label>
            <select name="carrera" required>
                <option value="">-- Seleccione una carrera --</option>
                @foreach($carreras as $carrera)
                    <option value="{{ $carrera->id_carrera }}">{{ $carrera->nombre }}</option>
                @endforeach
            </select>

            <label for="ciclo">Ciclo académico:</label>
            <select name="ciclo" required>
                <option value="">-- Seleccione un ciclo --</option>
                @foreach(range(1, 10) as $ciclo)
                    <option value="{{ $ciclo }}">{{ $ciclo }}</option>
                @endforeach
            </select>

            <label for="sede">Seleccione la sede:</label>
            <select name="sede" required>
                <option value="">-- Seleccione una sede --</option>
                @foreach ($sedes as $sede)
                    <option value="{{ $sede->id_sede }}">{{ $sede->nombre }} ({{ $sede->id_sede }})</option>
                @endforeach
            </select>

            <label for="pdf">Archivo PDF del acta:</label>
            <div>
                <input type="file" name="pdf" accept="application/pdf" required>
            </div>

            <div class="botones-final">
                <button type="submit">Procesamiento de Acta</button>
                <a href="{{ url('/') }}" class="btn-regresar">← Regresar al inicio</a>
            </div>
        </form>
    </div>

</body>
<style>
    
    input[type="file"] {
    box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f0f4f8;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .form-container {
        position: relative;
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 500px;
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #444;
    }

    select,
    input[type="file"] {
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .alert {
        padding: 10px;
        background-color: #e0f3e0;
        color: #2b662b;
        border: 1px solid #b8deb8;
        border-radius: 6px;
        margin-bottom: 15px;
    }

    .botones-final {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .botones-final button {
        padding: 12px 20px;
        font-size: 16px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .botones-final button:hover {
        background-color: #0056b3;
    }

    .btn-regresar {
        padding: 10px 18px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
    }

    .btn-regresar:hover {
        background-color: #5a6268;
    }
  
</style>
</html>