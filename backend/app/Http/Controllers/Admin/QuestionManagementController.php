<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionManagementController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::all();
        $selectedCourseId = $request->input('course_id', $courses->first()?->id);
        
        $questions = Question::when($selectedCourseId, function ($query) use ($selectedCourseId) {
            return $query->where('course_id', $selectedCourseId);
        })->latest()->get();

        return view('admin.questions.index', compact('questions', 'courses', 'selectedCourseId'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('admin.questions.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:pre_test,post_test,both',
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'wrong_answers' => 'required|array|size:3',
            'wrong_answers.*' => 'required|string',
        ]);

        // Merge correct answer and wrong answers
        $options = array_merge([$request->correct_answer], $request->wrong_answers);
        
        // Shuffle the options to make it random
        shuffle($options);

        // Find the index of the correct answer in the randomized options
        $correctOptionIndex = array_search($request->correct_answer, $options);

        Question::create([
            'course_id' => $request->course_id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'options' => $options,
            'correct_option' => $correctOptionIndex,
        ]);

        return redirect()->route('admin.questions.index', ['course_id' => $request->course_id])
            ->with('success', 'Soal ujian berhasil dibuat.');
    }

    public function edit(Question $question)
    {
        $courses = Course::all();
        
        $correctAnswer = $question->options[$question->correct_option] ?? '';
        
        $wrongAnswers = [];
        if (is_array($question->options)) {
            foreach ($question->options as $idx => $opt) {
                if ($idx != $question->correct_option) {
                    $wrongAnswers[] = $opt;
                }
            }
        }
        
        while (count($wrongAnswers) < 3) {
            $wrongAnswers[] = '';
        }

        return view('admin.questions.edit', compact('question', 'courses', 'correctAnswer', 'wrongAnswers'));
    }

    public function update(Request $request, Question $question)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'type' => 'required|in:pre_test,post_test,both',
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'wrong_answers' => 'required|array|size:3',
            'wrong_answers.*' => 'required|string',
        ]);

        // Merge correct answer and wrong answers
        $options = array_merge([$request->correct_answer], $request->wrong_answers);
        
        // Shuffle the options to make it random
        shuffle($options);

        // Find the index of the correct answer in the randomized options
        $correctOptionIndex = array_search($request->correct_answer, $options);

        $question->update([
            'course_id' => $request->course_id,
            'type' => $request->type,
            'question_text' => $request->question_text,
            'options' => $options,
            'correct_option' => $correctOptionIndex,
        ]);

        return redirect()->route('admin.questions.index', ['course_id' => $request->course_id])
            ->with('success', 'Soal ujian berhasil diperbarui.');
    }

    public function destroy(Question $question)
    {
        $courseId = $question->course_id;
        $question->delete();
        return redirect()->route('admin.questions.index', ['course_id' => $courseId])
            ->with('success', 'Soal ujian berhasil dihapus.');
    }
}
