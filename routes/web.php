<?php

use App\Http\Controllers\AsignaturaController;
use App\Http\Controllers\ClaseController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\PeriodoController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/fetch', [DashboardController::class, 'fetch']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/estudiantes', [EstudianteController::class, 'index'])->name('estudiantes.index');
    Route::get('/estudiantes/fetchDataTable', [EstudianteController::class, 'fetchDataTable']);

    Route::get('/profesores', [ProfesorController::class, 'index'])->name('profesores.index');
    Route::get('/profesores/fetchDataTable', [ProfesorController::class, 'fetchDataTable']);

    Route::get('/asignaturas', [AsignaturaController::class, 'index'])->name('asignaturas.index');
    Route::get('/asignaturas/fetchDataTable', [AsignaturaController::class, 'fetchDataTable']);

    Route::get('/grupos', [GrupoController::class, 'index'])->name('grupos.index');
    Route::get('/grupos/fetchDataTable', [GrupoController::class, 'fetchDataTable']);
    Route::get('/grupos/fechAllTables', [GrupoController::class, 'fechAllTables']);
    Route::get('/grupos/fetchDataById/{id}', [GrupoController::class, 'fetchDataById']);
    Route::get('/grupos/fetchGrupoEstudiantes/{id}', [GrupoController::class, 'fetchGrupoEstudiantes']);
    Route::get('/grupos/fetchGrupoAsignaturas/{id}', [GrupoController::class, 'fetchGrupoAsignaturas']);
    Route::post('/grupos', [GrupoController::class, 'store']);
    Route::post('/grupos/store_estudiantes', [GrupoController::class, 'store_estudiantes']);
    Route::post('/grupos/store_asignaturas', [GrupoController::class, 'store_asignaturas']);
    Route::delete('/grupos/estudiantes/{id}', [GrupoController::class, 'delete_estudiantes']);
    Route::delete('/grupos/asignaturas/{id}', [GrupoController::class, 'delete_asignaturas']);

    Route::get('/cursos', [CursoController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/indexEstudiante', [CursoController::class, 'indexEstudiante'])->name('cursos.index_estudiante');
    Route::get('/cursos/fetchDataTable', [CursoController::class, 'fetchDataTable']);
    Route::get('/cursos/{curso_id}', [CursoController::class, 'detalle'])->name('cursos.detalle');
    Route::get('/cursos/fetchGrupoEstudiantes/{id}', [CursoController::class, 'fetchGrupoEstudiantes']);
    Route::get('/cursos/fetchCursosByEstudiante/{estudiante_id}', [CursoController::class, 'fetchCursosByEstudiante']);
    Route::get('/cursos/fetchNotasByEstudiante/{curso_id}/{estudiante_id}', [CursoController::class, 'fetchNotasByEstudiante']);
    Route::post('/cursos/saveNota/', [CursoController::class, 'saveNota']);
    Route::post('/cursos/predecir/', [CursoController::class, 'predecir']);

    Route::get('/clases', [ClaseController::class, 'index'])->name('clases.index');
    Route::get('/clases/fetchDataTable', [ClaseController::class, 'fetchDataTable']);
    Route::get('/clases/fetchLoadForm', [ClaseController::class, 'fetchLoadForm']);
    Route::get('/clases/fetchGrupoEstudiantes/{clase_id}/{curso_id}', [ClaseController::class, 'fetchGrupoEstudiantes']);
    Route::post('/clases', [ClaseController::class, 'store']);
    Route::post('/clases/storeAsistencia/', [ClaseController::class, 'storeAsistencia']);
    Route::delete('/clases/{id}', [ClaseController::class, 'delete']);


    Route::get('/periodos', [PeriodoController::class, 'index'])->name('periodos.index');
    Route::get('/periodos/fetchDataTable', [PeriodoController::class, 'fetchDataTable']);
    Route::post('/periodos', [PeriodoController::class, 'store']);
    Route::delete('/periodos/{id}', [PeriodoController::class, 'delete']);

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/fetchDataTable', [UserController::class, 'fetchDataTable']);
    Route::get('/users/fetchDataById/{id}', [UserController::class, 'fetchDataById']);
    Route::post('/users', [UserController::class, 'store']);
    Route::post('/users/edit', [UserController::class, 'update']);

    Route::get('/roles/fetchAll', [RolController::class, 'fetchAll']);
});

require __DIR__.'/auth.php';
