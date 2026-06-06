<?php

namespace App\Http\Requests\Lesson;

use App\Models\Course;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // dd(['user_id'=>auth()->id(),'roles'=>auth()->user()?->getRoleNames(),'course_id'=>$this->input('course_id')]);
        $user=$this->user();
        if(!$user){
            return false;
        }
        if($user->hasRole('Admin')){
            return true;
        }
        if($user->hasRole('Instructor')){
            $courseId=$this->input('course_id');                    //input('course_id') or route('course_id')
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
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:1'],
            'is_published' => ['sometimes', 'boolean'],
            'videos' => ['nullable', 'array'],
            'videos.*' => ['file', 'mimetypes:video/mp4,video/quicktime,video/webm', 'max:512000'], // max 500MB
            'materials'=>['nullable','array'],
            'materials.*'=>['file','mimes:pdf','max:20480'],
        ];
    }
}
