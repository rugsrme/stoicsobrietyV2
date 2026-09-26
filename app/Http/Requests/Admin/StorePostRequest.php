<?php

namespace App\Http\Requests\Admin;

use App\Concerns\PostValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    use PostValidationRules;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->postRules();
    }
}
