<?php
namespace App\Http\Controllers;

use App\Exports\ExcelInvoiceExport;
use App\Services\InvoiceService;
use App\Http\Requests\InvoiceRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function invoice(InvoiceRequest $request)
    {
        $orderId = $request->route('order'); 
    $data = $this->invoiceService->getInvoiceData($orderId);

    return view('invoice', $data);
    }

    public function downloadInvoice(InvoiceRequest $request)
    {
        $orderId = $request->route('order');
        $order = $this->invoiceService->getInvoiceData($orderId)['order'];

        $fileType = $request->get('type', 'excel');  // default to 'excel' if no type provided

        if ($fileType === 'pdf') {
            return $this->downloadPdf($order);
        }

        // Default to Excel if type is not 'pdf'
        return $this->downloadExcel($order);
    }

    protected function downloadExcel($order)
    {
        $fileName = 'Invoice_Order_' . $order->id . '.xlsx';
    return Excel::download(new ExcelInvoiceExport($order), $fileName);
    }

    protected function downloadPdf($order)
    {
        $fileName = 'Invoice_Order_' . $order->id . '.pdf';

        $data = [
            'order' => $order,
            'supplier' => $order->product->user, 
        ];

        $pdf = Pdf::loadView('pdf-form.invoice', $data);  
        return $pdf->download($fileName);
    }



}