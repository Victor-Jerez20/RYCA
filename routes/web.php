<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActaNotasController;


Route::get('/', function () {
    return view('home');
});

// routes/web.php
Route::get('/acta/subir', [ActaNotasController::class, 'formulario'])->name('acta.formulario');
Route::post('/acta/procesar', [ActaNotasController::class, 'procesar'])->name('acta.procesar');
Route::post('/acta/guardar', [ActaNotasController::class, 'guardar'])->name('acta.guardar');
