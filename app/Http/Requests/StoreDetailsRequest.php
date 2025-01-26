<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StoreDetailsRequest extends FormRequest
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
            'ic_id' => 'required|exists:ics,id',
            'chip' => 'required|exists:images,id',
            'logic_diagram' => 'required|exists:images,id',
            'description' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'ic_id' => 'IC',
            'chip' => 'Chip Image',
            'logic_diagram' => 'Logic Diagram',
            'description' => 'Description',
        ];
    }
}
