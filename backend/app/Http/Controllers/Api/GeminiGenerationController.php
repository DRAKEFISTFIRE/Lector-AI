<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\StudyForm;
use App\Models\Syllabus;
use App\Services\DocumentTextExtractorService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiGenerationController extends Controller
{
    public function __construct(
        protected DocumentTextExtractorService $extractor,
        protected GeminiService $gemini,
    ) {
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'content_type' => ['required', 'in:curso,temario,test,formulario'],
            'title' => ['nullable', 'string', 'max:255'],
            'num_preguntas' => ['nullable', 'integer', 'min:3', 'max:30'],
            'num_items' => ['nullable', 'integer', 'min:3', 'max:30'],
            'dificultad' => ['nullable', 'in:facil,media,dificil'],
        ]);

        $file = $data['file'];
        $path = $file->store('course-sources', 'local');

        $course = $request->user()->courses()->create([
            'title' => $data['title'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'source_filename' => $file->getClientOriginalName(),
            'source_path' => $path,
            'status' => 'generating',
        ]);

        try {
            $text = $this->extractor->extract($file);

            $result = $this->gemini->generate($text, $data['content_type'], [
                'num_preguntas' => $data['num_preguntas'] ?? 10,
                'num_items' => $data['num_items'] ?? 12,
                'dificultad' => $data['dificultad'] ?? 'media',
            ]);

            DB::transaction(function () use ($course, $data, $result) {
                match ($data['content_type']) {
                    'curso' => $this->persistCurso($course, $result),
                    'temario' => $this->persistTemario($course, $result),
                    'test' => $this->persistTest($course, $result),
                    'formulario' => $this->persistFormulario($course, $result),
                };

                $course->update([
                    'status' => 'ready',
                    'title' => $result['titulo'] ?? $course->title,
                    'description' => $result['descripcion'] ?? $course->description,
                ]);
            });
        } catch (Throwable $e) {
            Log::error('Fallo generando contenido con Gemini', ['error' => $e->getMessage()]);
            $course->update([
                'status' => 'failed',
                'generation_error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'No se ha podido generar el contenido.',
                'error' => $e->getMessage(),
                'course' => $course,
            ], 422);
        }

        return response()->json(
            $course->fresh(['syllabi', 'quizzes.questions', 'studyForms.items']),
            201
        );
    }

    protected function persistCurso(Course $course, array $result): void
    {
        foreach ($result['unidades'] ?? [] as $i => $unidad) {
            Syllabus::create([
                'course_id' => $course->id,
                'unit_title' => $unidad['titulo'],
                'summary' => $unidad['resumen'] ?? null,
                'key_points' => $unidad['puntos_clave'] ?? [],
                'position' => $i,
            ]);
        }
    }

    protected function persistTemario(Course $course, array $result): void
    {
        $this->persistCurso($course, $result);
    }

    protected function persistTest(Course $course, array $result): void
    {
        $quiz = Quiz::create([
            'course_id' => $course->id,
            'title' => $result['titulo'] ?? 'Test generado',
        ]);

        foreach ($result['preguntas'] ?? [] as $i => $pregunta) {
            $quiz->questions()->create([
                'question' => $pregunta['pregunta'],
                'options' => $pregunta['opciones'] ?? [],
                'correct_option' => $pregunta['respuesta_correcta'] ?? '',
                'explanation' => $pregunta['explicacion'] ?? null,
                'position' => $i,
            ]);
        }
    }

    protected function persistFormulario(Course $course, array $result): void
    {
        $form = StudyForm::create([
            'course_id' => $course->id,
            'title' => $result['titulo'] ?? 'Formulario de estudio',
            'instructions' => $result['instrucciones'] ?? null,
        ]);

        foreach ($result['items'] ?? [] as $i => $item) {
            $form->items()->create([
                'prompt' => $item['prompt'],
                'expected_answer' => $item['respuesta_esperada'] ?? null,
                'type' => $item['tipo'] ?? 'flashcard',
                'position' => $i,
            ]);
        }
    }
}