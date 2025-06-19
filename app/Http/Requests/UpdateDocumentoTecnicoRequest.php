<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentoTecnicoRequest extends FormRequest
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
            'estado_id' => 'nullable|exists:estados,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'fecha_revalidacion' => 'required|date',
            'fecha_vigencia' => 'required|date',
            'modalidad_id' => 'nullable|array',
            'modalidad_id.*' => 'exists:modalidads,id',
            'nuevos_documentos_principales' => 'nullable|array',
            'nuevos_documentos_principales.*' => 'file|mimes:pdf|max:102400',
            'nuevos_documentos_anexos' => 'nullable|array',
            'nuevos_documentos_anexos.*' => 'file|mimes:pdf|max:102400',
            'archivos_a_eliminar' => 'nullable|array',
            'archivos_a_eliminar.*' => 'integer|exists:documento_archivos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nuevos_documentos_principales.required' => 'Debes subir al menos un documento principal.',
            'nuevos_documentos_principales.*.file' => 'Cada documento principal debe ser un archivo válido.',
            'nuevos_documentos_principales.*.mimes' => 'Los documentos principales deben estar en formato PDF.',
            'nuevos_documentos_principales.*.max' => 'Cada documento principal no debe superar los 100 MB.',

            'nuevos_documentos_anexos.*.file' => 'Cada documento anexo debe ser un archivo válido.',
            'nuevos_documentos_anexos.*.mimes' => 'Los documentos anexos deben estar en formato PDF.',
            'nuevos_documentos_anexos.*.max' => 'Cada documento anexo no debe superar los 100 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'empresa_id' => 'empresa',
            'tipo_de_documento_id' => 'documento',
            'estado_id' => 'estado',
            'departamento_id' => 'departamento',
            'fecha_revalidacion' => 'fecha de revalidación',
            'fecha_vigencia' => 'fecha de vigencia',
            'modalidad_id' => 'modalidad',
            'nuevos_documentos_principales' => 'documento principal',
            'nuevos_documentos_anexos' => 'documento anexo',
        ];
    }
public function withValidator($validator)
{
    $validator->after(function ($validator) {
        $documento = $this->route('documento');

        // Cuántos archivos principales tiene actualmente
        $archivosExistentes = $documento->archivos()
            ->where('tipo', 'principal')
            ->pluck('id');

        // Cuáles se están eliminando (según el request)
        $archivosAEliminar = collect($this->input('archivos_a_eliminar', []));

        // ¿Se eliminaron todos los principales?
        $eliminandoTodos = $archivosExistentes->every(fn($id) => $archivosAEliminar->contains($id));

        // ¿Se están subiendo nuevos archivos?
        $subiendoNuevos = $this->hasFile('nuevos_documentos_principales');

        // Si todos se están eliminando y no hay nuevos => error
        if ($eliminandoTodos && !$subiendoNuevos) {
            $validator->errors()->add('nuevos_documentos_principales', 'Debes mantener al menos un documento principal o subir uno nuevo.');
        }
    });
}


}