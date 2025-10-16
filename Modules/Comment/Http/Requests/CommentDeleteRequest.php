<?php

declare(strict_types=1);

namespace Modules\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Comment\Entities\Comment;

class CommentDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;

    }


}
