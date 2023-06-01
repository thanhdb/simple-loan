<?php

namespace App\Http\Requests\Loan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric|gt:0',
            'term' => 'required|integer|gt:0'
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Loan cannot be created without a loan amount.',
            'term.integer'    => 'Loan term must always be an integer.',
            'term.required'   => 'Loan cannot be created without a loan term.'
        ];
    }
}
