<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ============================================
// Rutas para el CRUD de Cursos
// ============================================

// GET - Consultar todos los Cusosos
Route::get('/cursos', [CursoAcademicoController::class, 'consultarCursos']); //

// GET - Consultar un Cursos específico por ID
Route::get('/cursos/{id}', [CursoAcademicoController::class, 'consultarCurso']);//

// POST - Insertar un nuevo Curso
Route::post('/cursos', [CursoAcademicoController::class, 'insertarCurso']);//

// PUT - Actualizar un curso completo
Route::put('/curso/{id}', [CursoAcademicoController::class, 'actualizarCurso']);//


// DELETE - Eliminar un Cursos
Route::delete('/curso/{id}', [CursoAcademicoController::class, 'eliminarCurso']);//


//Route::apiResource('cursos', CursoController::class);
