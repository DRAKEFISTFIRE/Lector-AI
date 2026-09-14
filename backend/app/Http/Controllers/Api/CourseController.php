<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = $request->user()->courses()
            ->withCount(['syllabi', 'quizzes', 'studyForms'])
            ->latest()
            ->get();

        return response()->json($courses);
    }

    public function show(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $course->load(['syllabi', 'quizzes.questions', 'studyForms.items']);

        return response()->json($course);
    }

    public function destroy(Request $request, Course $course)
    {
        $this->authorizeOwnership($request, $course);

        $course->delete();

        return response()->json(['message' => 'Curso eliminado.']);
    }

    protected function authorizeOwnership(Request $request, Course $course): void
    {
        abort_if($course->user_id !== $request->user()->id, 403, 'No tienes acceso a este curso.');
    }
}