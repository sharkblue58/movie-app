<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMovieRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'release_date' => 'sometimes|date',
            'description' => 'sometimes|string',
            'rating' => 'sometimes|numeric|min:0|max:10',
            'duration' => 'sometimes|integer',
            'poster_url' => 'nullable|url',
            'category_id' => 'sometimes|exists:categories,id',
        ];
    }
}
