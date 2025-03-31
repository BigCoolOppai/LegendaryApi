<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[A-ZА-ЯЁ]/u'], // Обязательно, строка, макс 255, первая буква заглавная (включая кириллицу)
            'last_name'  => ['required', 'string', 'max:255', 'regex:/^[A-ZА-ЯЁ]/u'], // Аналогично
            'patronymic' => ['required', 'string', 'max:255', 'regex:/^[A-ZА-ЯЁ]/u'], // Аналогично (используем patronymic, а не second_name)
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users,email'], // Обязательно, строка, email, макс 255, уникальный в таблице users
            'password'   => [
                'required',
                'string',
                Password::min(3) // Минимум 3 символа
                        ->letters()       // Должна быть хотя бы одна буква (любого регистра)
                        ->mixedCase()     // Должны быть буквы в верхнем и нижнем регистре
                        ->numbers(),      // Должна быть хотя бы одна цифра
                // ->symbols()       // Раскомментируйте, если нужны символы
                // ->uncompromised(), // Раскомментируйте для проверки по базам утечек (требует интернет)
            ],
            'birth_date' => ['required', 'date_format:Y-m-d'], // Обязательно, формат ГГГГ-ММ-ДД
        ];
    }
    /**
     * Get custom messages for validator errors.
     * Можно определить свои сообщения об ошибках.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.regex' => 'The first name must start with a capital letter.',
            'last_name.regex'  => 'The last name must start with a capital letter.',
            'patronymic.regex' => 'The patronymic must start with a capital letter.',
            'password.mixed_case' => 'The password must contain both uppercase and lowercase letters.',
            'password.letters' => 'The password must contain at least one letter.',
            'password.numbers' => 'The password must contain at least one number.',
            // Можно добавить другие кастомные сообщения
        ];
    }

    /**
     * Get custom attributes for validator errors.
     * Можно переименовать атрибуты в сообщениях об ошибках.
     *
     * @return array
     */
    public function attributes(): array
    {
         // Если в запросе приходит 'second_name', а в базе 'patronymic',
         // можно сделать так, чтобы в ошибке отображалось 'second_name'.
         // Но лучше использовать одинаковые имена.
         // Если вы все же принимаете 'second_name' в запросе:
         // return [
         //     'patronymic' => 'second_name',
         // ];
         // Если имена совпадают (patronymic и там и там), этот метод не нужен.
        return [];
    }
}
