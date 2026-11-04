<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\TestScore;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = User::where('role', 'student')->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function show(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        // Fetch all courses and attach user's high scores for pre_test and post_test
        $courses = \App\Models\Course::all();
        foreach ($courses as $course) {
            $course->pre_test_high = TestScore::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('type', 'pre_test')
                ->max('score');
            
            $course->post_test_high = TestScore::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('type', 'post_test')
                ->max('score');

            // Count attempts
            $course->pre_test_attempts = TestScore::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('type', 'pre_test')
                ->count();
            
            $course->post_test_attempts = TestScore::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('type', 'post_test')
                ->count();
        }

        return view('admin.students.show', compact('student', 'courses'));
    }

    public function resetScores(User $student, $courseId)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        TestScore::where('user_id', $student->id)
            ->where('course_id', $courseId)
            ->delete();

        return redirect()->route('admin.students.show', $student->id)
            ->with('success', 'Skor ujian untuk kursus tersebut berhasil direset ke 0.');
    }

    public function destroy(User $student)
    {
        if ($student->role !== 'student') {
            return redirect()->route('admin.students.index')->with('error', 'Hanya pengguna dengan peran siswa yang dapat dihapus dari menu ini.');
        }

        $student->delete();
        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }
}
