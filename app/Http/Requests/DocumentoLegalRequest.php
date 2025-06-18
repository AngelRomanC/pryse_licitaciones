<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentoLegalRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post'); // Detectar si es creación

        return [
            'nombre_documento' => 'required|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'tipo_de_documento_id' => 'required|exists:tipo_de_documentos,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',         

            // Archivos principales (requeridos solo en creación)
            'ruta_documento' => $isCreate ? 'required|array' : 'nullable|array',
            'ruta_documento.*' => 'file|mimes:pdf|max:102400',

            // Archivos anexos (opcionales)
            'ruta_documento_anexo' => 'nullable|array',
            'ruta_documento_anexo.*' => 'file|mimes:pdf|max:102400',
        ];
    }

    public function messages(): array
    {
        return [
            'ruta_documento.required' => 'Debes subir al menos un documento principal.',
            'ruta_documento.*.file' => 'Cada documento principal debe ser un archivo válido.',
            'ruta_documento.*.mimes' => 'Los documentos principales deben estar en formato PDF.',
            'ruta_documento.*.max' => 'Cada documento principal no debe superar los 100 MB.',

            'ruta_documento_anexo.*.file' => 'Cada documento anexo debe ser un archivo válido.',
            'ruta_documento_anexo.*.mimes' => 'Los documentos anexos deben estar en formato PDF.',
            'ruta_documento_anexo.*.max' => 'Cada documento anexo no debe superar los 100 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'empresa_id' => 'empresa',
            'tipo_de_documento_id' => 'documento',
            'departamento_id' => 'departamento',
            'fecha_revalidacion' => 'fecha de revalidación',
            'fecha_vigencia' => 'fecha de vigencia',
            'ruta_documento' => 'documento principal',
            'ruta_documento_anexo' => 'documento anexo',
        ];
    }
}