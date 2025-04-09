<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovieRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'release_date' => 'required|date',
            'description' => 'required|string',
            'rating' => 'required|numeric|min:0|max:10',
            'duration' => 'required|integer',
            'poster_url' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'category_ids' => 'required|array|exists:categories,id',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}
