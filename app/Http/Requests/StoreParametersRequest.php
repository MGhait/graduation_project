<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreParametersRequest extends FormRequest
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
            'technology_family' => [
                'required',
                'string',
                Rule::unique('parameters')->where('ic_details_id', $this->input('ic_details_id'))
            ],
            'min_voltage' => 'required|numeric|min:0',
            'max_voltage' => 'required|numeric|gt:min_voltage',
            'channels_number' => 'required|integer',
            'inputs_per_channel' => 'required|integer',
            'min_temperature' => 'required|numeric',
            'max_temperature' => 'required|numeric|gt:min_temperature',

        ];
    }

    public function attributes(): array
    {
        return [
            'ic_details_id' => 'IC Details',
            'technology_family' => 'Technology Family',
            'min_voltage' => 'Minimum Voltage',
            'max_voltage' => 'Maximum Voltage',
            'channels_number' => 'Channels Number',
            'inputs_per_channel' => 'Inputs Per Channel',
            'min_temperature' => 'Minimum Temperature',
            'max_temperature' => 'Maximum Temperature',
        ];
    }
}
