<?php

namespace Modules\Invoices\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'customer_name'    => [
                'required',
                'string',
                'max:255'
            ],
            'customer_email'   => [
                'required',
                'email',
                'max:255'
            ],
            'product_lines' => [
                'nullable',
                'array'
            ],
            'product_lines.*.name' => [
                'name'     => ["required"],
            ],
            'product_lines.*.quantity' => [
                "required",
                'int',
                'min:1'
            ],
            'product_lines.*.price'    => [
                "required",
                'int',
                'min:1'
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
