<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Question;
use App\Models\TestScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function show(string $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        
        $preTestScore = null;
        $postTestScore = null;

        if (Auth::check()) {
            $preTestScore = TestScore::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('type', 'pre_test')
                ->max('score');
            
            $postTestScore = TestScore::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('type', 'post_test')
                ->max('score');
        }

        return view('courses.show', compact('course', 'preTestScore', 'postTestScore'));
    }

    public function showTest(string $slug, string $type)
    {
        if (!in_array($type, ['pre_test', 'post_test'])) {
            abort(404);
        }

        $course = Course::where('slug', $slug)->firstOrFail();
        $questions = Question::where('course_id', $course->id)
            ->whereIn('type', [$type, 'both'])
            ->get();

        if ($questions->isEmpty()) {
            return redirect()->route('courses.show', $slug)->with('error', 'Soal ujian belum tersedia untuk tes ini.');
        }

        // Shuffle options for each question dynamically
        foreach ($questions as $question) {
            if (is_array($question->options)) {
                $opts = $question->options;
                shuffle($opts);
                $question->options = $opts;
            }
        }

        return view('courses.test', compact('course', 'questions', 'type'));
    }

    public function submitTest(Request $request, string $slug, string $type)
    {
        if (!in_array($type, ['pre_test', 'post_test'])) {
            return response()->json(['error' => 'Invalid test type'], 400);
        }

        $course = Course::where('slug', $slug)->firstOrFail();
        $questions = Question::where('course_id', $course->id)
            ->whereIn('type', [$type, 'both'])
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['error' => 'Questions not found'], 404);
        }

        $answers = $request->input('answers', []);
        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($questions as $question) {
            $questionId = $question->id;
            $submittedAnswer = $answers[$questionId] ?? null;
            
            $correctAnswerText = $question->options[$question->correct_option] ?? '';
            
            if ($submittedAnswer !== null && trim($submittedAnswer) === trim($correctAnswerText)) {
                $correctCount++;
            }
        }

        // Calculate score out of 100
        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100) : 0;

        // Save score to database
        TestScore::create([
            'user_id' => Auth::id(),
            'course_id' => $course->id,
            'type' => $type,
            'score' => $score,
        ]);

        $message = '';
        $comparePreTest = null;

        if ($type === 'pre_test') {
            if ($score < 100) {
                $message = "Skor Anda adalah {$score}. Disarankan untuk mempelajari materi HTML Dasar di bawah ini terlebih dahulu.";
            } else {
                $message = "Luar biasa! Skor Anda sempurna ({$score}). Anda disarankan untuk lanjut ke kursus berikutnya.";
            }
        } else { // post_test
            // Get highest pre-test score to compare
            $comparePreTest = TestScore::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->where('type', 'pre_test')
                ->max('score');

            if ($score < 100) {
                $message = "Skor Post Test Anda adalah {$score}. Silakan pelajari kembali materinya dan Anda bisa mengulang tes ini.";
            } else {
                $message = "Selamat! Anda mendapatkan skor sempurna ({$score}) pada Post Test. Anda disarankan untuk lanjut ke kursus berikutnya.";
            }
        }

        return response()->json([
            'success' => true,
            'score' => $score,
            'type' => $type,
            'compare_pre_test' => $comparePreTest,
            'message' => $message
        ]);
    }
}
