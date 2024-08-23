<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
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
    public function rules()
    {
        return [
            'aula' => 'required',
            'carrera' => 'required',
            'materia' => 'required',
            'numAlumnos' => 'required|integer|min:1',
            'comentario' => 'nullable|string|max:500',
            'software' => 'required|array',
            'software.*' => 'exists:software,idSoftware', // Valida que cada software exista en la tabla
        ];
    }

    public function messages()
    {
        return [
            'aula.required' => 'El aula es obligatoria.',
            'carrera.required' => 'La carrera es obligatoria.',
            'materia.required' => 'La materia es obligatoria.',
            'numAlumnos.required' => 'El número de alumnos es obligatorio.',
            'numAlumnos.integer' => 'El número de alumnos debe ser un número entero.',
            'numAlumnos.min' => 'Debe haber al menos un alumno.',
            'software.required' => 'Debes seleccionar al menos un software.',
            'software.exists' => 'El software seleccionado no es válido.',
        ];
    }
}
