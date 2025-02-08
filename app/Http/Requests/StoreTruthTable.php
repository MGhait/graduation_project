<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreTruthTable extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->is('api/*')){
            $response = ApiResponse::sendResponse(422,'Validation Error', $validator->errors());
            throw new ValidationException($validator, $response);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'input' => 'required|string',
            'output' => 'required|boolean',
            'ic_id' => 'required|exists:ics,id',
        ];
    }

    public function attributes()
    {
        return [
            'input' => 'Inputs',
            'output' => 'Outputs',
            'ic_id' => 'IC',
        ];
    }
}
