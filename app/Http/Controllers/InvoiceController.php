<?php
namespace App\Http\Controllers;

use App\Exports\ExcelInvoiceExport;
use App\Services\InvoiceService;
use App\Http\Requests\InvoiceRequest;
use App\Models\Order;
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
        public function sellerinvoice($orderId)
    {
        $user = auth()->user();

        $order = Order::with([
            'items.product',
            'items.variant',
            'items.seller'
        ])->findOrFail($orderId);

        // 🔥 ONLY SELLER ITEMS (IMPORTANT)
        $items = $order->items
            ->where('seller_id', $user->id)
            ->values();

        // If seller tries to open someone else's order
        if ($items->isEmpty()) {
            abort(403, 'No items found for this seller in this order.');
        }

        // supplier = current seller
        $supplier = $user;

        return view('seller-invoice', compact('order', 'items', 'supplier', 'user'));
    }


public function downloadInvoice(InvoiceRequest $request)
{
    $orderId = $request->route('order');

    $order = Order::with(['items.product','items.variant','items.seller'])
        ->findOrFail($orderId);

    $user = auth()->user();

    // =========================
    // CASE 1: CUSTOMER (owner)
    // =========================
    if ($order->user_id === $user->id) {

        $items = $order->items; // full invoice

    }

    // =========================
    // CASE 2: SELLER (participating items)
    // =========================
    else {

        $items = $order->items
            ->where('seller_id', $user->id)
            ->values();

        // 🔥 IMPORTANT FIX (no 403 blindly)
        if ($items->isEmpty()) {
            return response()->json([
                'message' => 'You are not part of this order'
            ], 403);
        }
    }

    $supplier = $user;

    $fileType = $request->get('type','excel');

    if ($fileType === 'pdf') {
        return $this->downloadPdf($order, $items, $supplier);
    }

    return $this->downloadExcel($order, $items, $supplier);
}

    protected function downloadExcel($order, $items, $supplier)
{
    $fileName = 'Invoice_Order_' . $order->id . '.xlsx';

    return Excel::download(
        new ExcelInvoiceExport($order, $items, $supplier),
        $fileName
    );
}

protected function downloadPdf($order, $items, $supplier)
{
    $fileName = 'Invoice_Order_' . $order->id . '.pdf';

    $data = [
        'order' => $order,
        'items' => $items,
        'supplier' => $supplier,
    ];
    $pdf = Pdf::loadView('pdf-form.invoice', $data);

    return $pdf->download($fileName);
}


}