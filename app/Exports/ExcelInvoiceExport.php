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
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function array(): array
    {
        $order = $this->order;
        $product = $order->product;
        $variant = $order->variant;
        $supplier = $product->user;

        $total = $order->price * $order->quantity;

        
          return [
    ['Invoice - Order #' . $order->id],
    [],

    ['Supplier Details', '', '','', 'Customer Details',  '', '', ''],

    ['Name', '', $supplier->name ?? 'N/A', '', 'Name', '', $order->customer_name ?? 'N/A', ''],
    ['Email', '', $supplier->email ?? 'N/A', '', 'Email', '', $order->email ?? 'N/A', ''],
    ['Address', '', $supplier->address ?? 'N/A', '', 'Address', '', $order->address ?? 'N/A', ''],
    ['Payment Mode', '', ucfirst($order->payment_mode), '', '', '', '', ''],

    [
        "Order Date:", '', $order->order_date->format('d M Y'), '',
        "Dispatch Date:", '', $order->dispatch_date?->format('d M Y') ?? 'Not yet dispatched', ''
    ],
    [''],

    ['#','Product','Color','Size','Gender','Quantity','Price','Total'],

    [1,$product->name,$variant->color ?? '-', $variant->size ?? '-', $variant->gender ?? '-', $order->quantity, $order->price, $total],

    ['Grand Total','','','','','','',$total],

    [''],
    ['Thank you for your purchase!']
];
    }

    public function registerEvents(): array
    {
       return [
        AfterSheet::class => function(AfterSheet $event){

            $sheet = $event->sheet->getDelegate();

           
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
            $sheet->getStyle('A1')->getFont()->getColor()->setRGB('ff8c00');
            $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

            $sheet->mergeCells('A11:G11');
            $sheet->getStyle('A11')->getAlignment()->setHorizontal('left');
            $sheet->mergeCells('A2:D2');
            $sheet->mergeCells('E2:H2');

            $sheet->getStyle('A2:H2')->applyFromArray([
                'fill' => [
                    'fillType' => 'solid',
                    'color' => ['rgb' => '333333']
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'ff8c00']
                ],
                 'alignment' => [
                    'horizontal' => 'center'
                ]
            ]);

            
            for ($i = 3; $i <= 7; $i++) {
                $sheet->mergeCells("A$i:B$i");
                $sheet->mergeCells("C$i:D$i");

                $sheet->mergeCells("E$i:F$i");
                $sheet->mergeCells("G$i:H$i");
            }

            $sheet->getStyle('A3:A7')->getFont()->setBold(true);
            $sheet->getStyle('E3:E7')->getFont()->setBold(true);

            $sheet->getStyle('A3:D7')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            $sheet->getStyle('E3:H7')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            $sheet->getStyle('A8:H8')->getFont()->setBold(true);
            $sheet->getStyle('A11:H11')->getFont()->setBold(true);
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

            $sheet->getStyle('A9:H11')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => 'thin',
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            $sheet->getStyle('A9:H11')->getAlignment()->setHorizontal('center');

            $sheet->mergeCells('A13:G13');
            $sheet->getStyle('A13:H13')->getFont()->setBold(true);

            
            $sheet->mergeCells('A13:H13');
            $sheet->getStyle('A13')->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A13')->getFont()->getColor()->setRGB('ff8c00');
            $sheet->getStyle('C4:D7')->getAlignment()->setHorizontal('left');
            $sheet->getStyle('G4:H7')->getAlignment()->setHorizontal('left');
        }
    ];
    }

    public function title(): string
    {
        return 'Invoice #' . $this->order->id;
    }
}