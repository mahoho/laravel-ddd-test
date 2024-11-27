<?php

namespace Modules\Invoices\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id'      => ['required', 'exists:invoices,id'],
            'subject' => ['required', 'string'],
            'message' => ['required', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
