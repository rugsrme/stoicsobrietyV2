<?php

namespace App\Http\Requests\Admin;

class UpdateReaderReviewRequest extends StoreReaderReviewRequest
{
    /**
     * Fields are optional so the list page can flip a single checkbox.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_map(
            fn (array $rules) => ['sometimes', ...$rules],
            parent::rules(),
        );
    }
}
