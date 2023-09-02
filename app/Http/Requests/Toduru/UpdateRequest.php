<?php

namespace App\Http\Requests\Toduru;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        return [
            // 必須かつ100文字以内
            'toduru' => 'required|max:100'
        ];
    }

    public function toduru(): string
    {
        return $this->input('toduru');
    }

    public function id(): int
    {
        // ID取得はrequestに書くと簡略化できる。
        return (int) $this->route('toduruId');
    }
}
