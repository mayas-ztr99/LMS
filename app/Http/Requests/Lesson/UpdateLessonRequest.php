<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Lesson;
class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        $lesson = $this->route('lesson');

        if (! $user || ! $lesson instanceof Lesson) {
            return false;
        }

        if ($user->hasRole('Admin')) {
            return true;
        }

        if ($user->hasRole('Instructor')) {
            $courseId = $this->input('course_id', $lesson->course_id);
            if(!$courseId){return false;}
            return Course::query()
                ->whereKey($courseId)
                ->whereHas('instructors', function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                })
                ->exists();
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $lessonId = $this->route('lesson')->id;
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'sort_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('lessons')
                    ->ignore($lessonId)
                    ->where(fn ($query) => $query->where('course_id', $this->input('course_id'))),
            ],
            'is_published' => ['sometimes', 'boolean'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:512000'],
            'materials' => ['nullable', 'array'],
            'materials.*' => ['file', 'mimes:pdf', 'max:20480'],
        ];
    }
}
