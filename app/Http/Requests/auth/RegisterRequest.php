<?php

namespace App\Http\Requests\auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'username' => 'required|unique:users,username',
            'phoneNumber' => 'required|numeric|digits:10',
            'uid' => 'required|min:6',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ];
    }
}
