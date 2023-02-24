<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
        if(isset($this->id)){
            return [
                'role_name'=>'required|unique:acl_roles,role_name,'.$this->id.',id',
            ];
        }
             return [
                 'role_name'=>'required|unique:acl_roles'
             ];
    }
}
