<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StorePackagesRequest extends FormRequest
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
            'ic_details_id' => 'required|exists:ic_details,id',
            'name' => [
                'required',
                'string',
                Rule::unique('packages')->where('ic_details_id', $this->input('ic_details_id'))
            ],
            'num' => 'required|integer',
            'size' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'ic_details_id' => 'IC Details',
            'name' => 'Package Name',
            'pins' => 'Number of Pins',
            'size' => 'Size',
        ];
    }
}
