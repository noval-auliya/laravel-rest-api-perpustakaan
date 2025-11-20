<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BukuRequest extends FormRequest
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
            'kategori_buku_id'  => 'required|exists:kategori_buku,id',
            'judul'             => ['required','string','max:255', Rule::unique('buku', 'judul')->ignore($this->route('buku'))],
            'pengarang'         => 'nullable|string|max:255',
            'penerbit'          => 'nullable|string|max:255',
            'isbn'              => 'nullable|string|max:50',
            'stok'              => 'required|integer|min:0',
            'deskripsi'         => 'nullable|string',
        ];
    }
}
