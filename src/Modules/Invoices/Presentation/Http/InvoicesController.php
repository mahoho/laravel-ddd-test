<?php

namespace Modules\Invoices\Presentation\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Presentation\Http\Requests\CreateInvoiceRequest;
use Modules\Invoices\Presentation\Http\Requests\SendInvoiceRequest;
use Symfony\Component\HttpFoundation\Response;

class InvoicesController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function view(string $id)
    {
        $invoice = $this->invoiceService->viewInvoice($id);

        if(!$invoice) {
            return new JsonResponse(data: null, status: Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse(data: $invoice, status: Response::HTTP_OK);
    }

    public function create(CreateInvoiceRequest $request)
    {
        $customerName = $request->input('customer_name');
        $customerEmail = $request->input('customer_email');
        $productLines = $request->input('product_lines', []);

        $invoice = $this->invoiceService->createInvoice($customerName, $customerEmail, $productLines);

        return new JsonResponse(data: $invoice, status: Response::HTTP_OK);
    }

    public function send(SendInvoiceRequest $request)
    {
        $id = $request->input('id');
        $subject = $request->input('subject');
        $message = $request->input('message');

        $this->invoiceService->sendInvoice($id, $subject, $message);

        return new JsonResponse(data: null, status: Response::HTTP_OK);
    }
}
