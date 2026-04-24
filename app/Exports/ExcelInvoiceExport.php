<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class ExcelInvoiceExport implements FromArray, WithEvents, ShouldAutoSize, WithTitle
{
   protected $order, $items, $supplier;

public function __construct($order, $items, $supplier)
{
    $this->order = $order;
    $this->items = $items;         // ✅ filtered items
    $this->supplier = $supplier;   // ✅ correct seller
}

    public function array(): array
    {
        $order = $this->order;
        $items = $this->items;

        $supplier = $this->supplier;

        $rows = [];

        // HEADER
        $rows[] = ['Invoice - Order #' . $order->id];
        $rows[] = [];

        $rows[] = ['Supplier Details', '', '', '', 'Customer Details', '', '', ''];

        $rows[] = ['Name', '', $supplier->name ?? 'N/A', '', 'Name', '', $order->customer_name ?? 'N/A', ''];
        $rows[] = ['Email', '', $supplier->email ?? 'N/A', '', 'Email', '', $order->email ?? 'N/A', ''];
        $rows[] = ['Address', '', $supplier->address ?? 'N/A', '', 'Address', '', $order->address ?? 'N/A', ''];
        $rows[] = ['Payment Mode', '', ucfirst($order->payment_mode), '', '', '', '', ''];

        $rows[] = [
            "Order Date:", '', optional($order->order_date)->format('d M Y'), '',
            "Dispatch Date:", '', optional($order->dispatch_date)->format('d M Y') ?? 'Not dispatched', ''
        ];

        $rows[] = ['', '', '', '', '', '', '', ''];

        // TABLE HEADER
        $rows[] = ['#','Product','Color','Size','Gender','Quantity','Price','Total'];

        $grandTotal = 0;

        foreach ($items as $index => $item) {

            $product = $item->product;
            $variant = $item->variant;

            $price = $item->price ?? 0;
            $qty   = $item->quantity ?? 0;
            $total = $price * $qty;

            $grandTotal += $total;

            $rows[] = [
                $index + 1,
                $product->name ?? 'N/A',
                $variant->color ?? '-',
                $variant->size ?? '-',
                $variant->gender ?? '-',
                $qty,
                $price,
                $total
            ];
        }

        // GRAND TOTAL
        $rows[] = ['Grand Total','','','','','','',$grandTotal];

        $rows[] = [];
        $rows[] = ['Thank you for your purchase!'];

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){

                $sheet = $event->sheet->getDelegate();

                // Title
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
                $sheet->getStyle('A1')->getFont()->getColor()->setRGB('ff8c00');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // Section header
                $sheet->getStyle('A2:H2')->getFont()->setBold(true);

                // Table header row (dynamic now → always row 10)
                $sheet->getStyle('A9:H9')->applyFromArray([
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => '333333']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'ffffff']
                    ],
                    'alignment' => [
                        'horizontal' => 'center'
                    ]
                ]);

                // Borders for all table
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A9:H{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }

    public function title(): string
    {
        return 'Invoice #' . $this->order->id;
    }
}