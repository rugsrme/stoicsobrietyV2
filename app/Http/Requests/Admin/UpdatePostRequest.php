<?php

namespace App\Http\Requests\Admin;

use App\Concerns\PostValidationRules;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    use PostValidationRules;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Post $post */
        $post = $this->route('post');

        return $this->postRules($post);
    }
}
