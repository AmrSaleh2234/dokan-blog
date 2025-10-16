<?php

declare(strict_types=1);

namespace Modules\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }
}
