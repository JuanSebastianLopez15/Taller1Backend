<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CursoAcademicoController extends Controller
{
    // Ruta del archivo JSON para almacenar los estudiantes
    private $archivoCursos = 'cursos.json';

    /**
     * Datos iniciales de estudiantes
     */
    private function getDatosIniciales(){
        return [
            [
                'id' => 1,
                'nombre' => 'primeros auxilios',
                'codigo' => 1001,
                'creditos' => '2'
                'docente' => 'Harold M'
            ],
            [
                'id' => 2,
                'nombre' => 'Backend',
                'codigo' => 1002,
                'creditos' => '3'
                'docente' => 'Harold Morales'
            ],
            [
                'id' => 3,
                'nombre' => 'Logica de programacion',
                'codigo' => 1003,
                'creditos' => '3'
                'docente' => 'Simon'
            ]
        ];
    }

    /**
     * Método auxiliar para obtener el array de cursos desde el archivo
     */
    private function getCursos(){
        // Si el archivo no existe, crear con datos iniciales
        if (!Storage::exists($this->archivoCursos)) {
            $this->guardarCursos($this->getDatosIniciales());
            return $this->getDatosIniciales();
        }

        // Leer el archivo JSON
        $contenido = Storage::get($this->archivoCursos);
        $cursos = json_decode($contenido, true);

        // Si hay error al decodificar, retornar datos iniciales
        if ($cursos === null) {
            return $this->getDatosIniciales();
        }

        return $cursos;
    }

    /**
     * Método auxiliar para guardar el array de estudiantes en el archivo
     */
    private function guardarCursos($cursos){
        Storage::put($this->archivoCursos, json_encode($cursos, JSON_PRETTY_PRINT));
    }

    /**
     * GET - Consultar todos los estudiantes
     * Método: GET
     * URL: /api/estudiantes
     */
    public function consultarCursos(){
        return response()->json([
            "message" => "Lista de Cursos obtenida exitosamente",
            "data" => $this->getCursos()
        ], 200);
    }

    /**
     * GET - Consultar un estudiante específico por ID
     * Método: GET
     * URL: /api/estudiantes/{id}
     */
    public function consultarCurso($id){
        $cursos = $this->getCursos();
        $curso = collect($Cursos)->firstWhere('id', $id);

        if (!$cursos) {
            return response()->json([
                "message" => "Curso no encontrado"
            ], 404);
        }

        return response()->json([
            "message" => "Curso encontrado exitosamente",
            "data" => $curso
        ], 200);
    }

    /**
     * POST - Insertar un nuevo estudiante
     * Método: POST
     * URL: /api/estudiantes
     * Body (JSON): { "nombre": "Nombre", "edad": 20, "carrera": "Carrera" }
     */
    public function insertarCurso(Request $request){
        // Validar los datos recibidos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|integer|min:1',
            'creditos' => 'required|string|max:255'
            'docente' => 'required|string|max:255'
        ]);

        // Obtener estudiantes actuales
        $cursos = $this->getCursos();

        // Crear el nuevo estudiante
        $nuevoCurso = [
            'id' => count($cursos) > 0 ? max(array_column($cursos, 'id')) + 1 : 1,
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'creditos' => $request->creditos,
            'docente' => $request->docente

        ];

        // Agregar al array
        $cursos[] = $nuevoCurso;

        // Guardar en el archivo
        $this->guardarCursos($Cursos);

        return response()->json([
            "message" => "Curso insertado exitosamente",
            "data" => $nuevoCurso
        ], 201);
    }

    /**
     * PUT - Actualizar un estudiante completo
     * Método: PUT
     * URL: /api/estudiantes/{id}
     * Body (JSON): { "nombre": "Nombre", "edad": 20, "carrera": "Carrera" }
     */
    public function actualizarCurso(Request $request, $id){
        // Validar los datos recibidos
        $request->validate([
            'nombre' => 'required|string|max:255',
            'edad' => 'required|integer|min:1',
            'carrera' => 'required|string|max:255'
        ]);

        // Obtener estudiantes actuales
        $cursos = $this->getCursos();

        // Buscar el estudiante
        $indice = collect($cursos)->search(function($item) use ($id) {
            return $item['id'] == $id;
        });

        if ($indice === false) {
            return response()->json([
                "message" => "Curso no encontrado"
            ], 404);
        }

        // Actualizar el estudiante
        $cursos[$indice] = [
            'id' => (int)$id,
            'nombre' => $request->nombre,
            'edad' => $request->edad,
            'carrera' => $request->carrera
        ];

        // Guardar en el archivo
        $this->guardarCursos($cursos);

        return response()->json([
            "message" => "Curso actualizado exitosamente",
            "data" => $cursos[$indice]
        ], 200);
    }

    /**
     * PATCH - Actualizar un estudiante parcialmente (solo los campos enviados)
     * Método: PATCH
     * URL: /api/estudiantes/{id}
     * Body (JSON): { "nombre": "Solo actualizar nombre" } o { "edad": 25 } o cualquier combinación
     */
    public function actualizarCursoParcial(Request $request, $id){
        // Validar los datos recibidos (todos son opcionales en PATCH)
        $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'edad' => 'sometimes|integer|min:1',
            'carrera' => 'sometimes|string|max:255'
        ]);

        // Obtener estudiantes actuales
        $cursos = $this->getCursos();

        // Buscar el estudiante
        $indice = collect($cursos)->search(function($item) use ($id) {
            return $item['id'] == $id;
        });

        if ($indice === false) {
            return response()->json([
                "message" => "Curso no encontrado"
            ], 404);
        }

        // Actualizar solo los campos que se enviaron
        if ($request->has('nombre')) {
            $Cursos[$indice]['nombre'] = $request->nombre;
        }

        if ($request->has('edad')) {
            $cursos[$indice]['edad'] = $request->edad;
        }

        if ($request->has('carrera')) {
            $cursos[$indice]['carrera'] = $request->carrera;
        }

        // Guardar en el archivo
        $this->guardarCursos($cursos);

        return response()->json([
            "message" => "Curso actualizado parcialmente exitosamente",
            "data" => $cursos[$indice]
        ], 200);
    }

    /**
     * DELETE - Eliminar un estudiante
     * Método: DELETE
     * URL: /api/estudiantes/{id}
     */
    public function eliminarCurso($id){
        // Obtener estudiantes actuales
        $cursos = $this->getCursos();

        // Buscar el estudiante
        $indice = collect($cursos)->search(function($item) use ($id) {
            return $item['id'] == $id;
        });

        if ($indice === false) {
            return response()->json([
                "message" => "Curso no encontrado"
            ], 404);
        }

        // Guardar el estudiante antes de eliminarlo
        $cursoEliminado = $cursos[$indice];

        // Eliminar del array
        unset($cursos[$indice]);
        $cursos = array_values($cursos); // Reindexar el array

        // Guardar en el archivo
        $this->guardarCursos($cursos);

        return response()->json([
            "message" => "Curso eliminado exitosamente",
            "data" => $CursoEliminado
        ], 200);
    }
}
