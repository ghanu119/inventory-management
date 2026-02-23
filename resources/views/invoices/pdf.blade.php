@php
    $L = [
        'tax_invoice' => 'TAX INVOICE',
        'bill_to' => 'Bill To:',
        'invoice_no' => 'Invoice #:',
        'date' => 'Date:',
        'payment_mode' => 'Payment Mode:',
        'due' => 'Due:',
        'product' => 'Product',
        'qty' => 'Qty',
        'price' => 'Price',
        'gst_rate' => 'GST Rate',
        'taxable_amount' => 'Taxable Amount',
        'subtotal' => 'Subtotal:',
        'total_gst' => 'Total GST:',
        'discount' => 'Discount:',
        'grand_total' => 'Grand Total:',
        'notes' => 'Notes:',
        'terms' => 'Terms and conditions',
        'customer_signature' => 'Customer signature',
        'hsn' => 'HSN:',
        'serial_no' => 'Serial No:',
        'warranty' => 'Warranty:',
        'years' => 'years',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body,pre{font-family:notosansgujarati,dejavusans,sans-serif;font-size:12px;margin:0;padding:10px;line-height:1.35;color:#222;}
        .header{width:100%;border-bottom:2px solid #222;padding:8px 0 10px 0;margin-bottom:12px;}
        .header-table{width:100%;border:none;border-collapse:collapse;}
        .header-table td{border:none;padding:0;vertical-align:top;}
        .header-table .col-left{width:50%;text-align:left;}
        .header-table .col-right{width:50%;text-align:right;}
        .invoice-title{margin:0;padding:0;font-size:24px;font-weight:700;letter-spacing:0.5px;color:#111;}
        .company-name{margin:0 0 4px 0;padding:0;font-size:15px;font-weight:700;color:#111;}
        .header-meta{margin:3px 0;font-size:12px;line-height:1.4;color:#333;}
        .company-logo{display:none}
        .details-table{width:100%;border:none;border-collapse:collapse;margin-top:12px;}
        .details-table td{border:none;padding:0 8px 0 0;vertical-align:top;}
        .details-table td.col-right{padding:0 0 0 8px;}
        .details-table .col-left{width:50%;text-align:left;}
        .details-table .col-right{width:50%;text-align:right;}
        .details-table .section-label{margin:0 0 10px 0;font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;line-height:1.4;color:#333;}
        .details-table .section-value{margin:6px 0;font-size:13px;line-height:1.6;letter-spacing:0.02em;color:#222;}
        .details-table .section-value strong{font-size:13px;font-weight:700;color:#111;letter-spacing:0.01em;}
        .section{margin-bottom:10px;}
        .section-label{margin:0 0 4px 0;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px;color:#444;}
        .section-value{margin:3px 0;font-size:11px;line-height:1.4;color:#222;}
        .section-value strong{color:#111;}
        table.items{width:100%;border-collapse:collapse;margin-top:10px;font-size:13px;}
        table.items th,table.items td{border:1px solid #ccc;padding:7px 10px;text-align:left;}
        table.items th{background:#e8e8e8;font-weight:700;font-size:12px;text-transform:uppercase;letter-spacing:0.2px;}
        table.items td{vertical-align:top;line-height:1.4;}
        table.items .item-product-name{font-size:13px;font-weight:700;color:#111;}
        .item-extra{font-size:12px;color:#444;margin-top:5px;line-height:1.45;}
        .text-right{text-align:right;}
        table.items .cell-nowrap{white-space:nowrap;}
        table.items .cell-product{min-width:0;}
        .total-section{margin-top:14px;width:280px;margin-left:auto;clear:both;border:1px solid #ccc;background:#fafafa;padding:10px 14px;}
        .total-section .total-table{width:100%;border:none;border-collapse:collapse;font-size:12px;}
        .total-section .total-table td{border:none;padding:5px 0;vertical-align:middle;line-height:1.4;}
        .total-section .total-table td:first-child{text-align:left;font-weight:600;color:#333;}
        .total-section .total-table td:last-child{text-align:right;font-variant-numeric:tabular-nums;}
        .total-section .total-table tr.grand-total td{border-top:2px solid #222;padding-top:8px;margin-top:4px;font-weight:700;font-size:15px;color:#111;}
        .total-section .total-table tr.grand-total td:last-child{font-size:15px;}
        .terms-block{margin-top:14px;padding-top:10px;border-top:1px solid #ddd;clear:both;font-size:11px;line-height:1.35;font-family:notosansgujarati,dejavusans,sans-serif;color:#333;}
        .terms-block .terms-heading{font-size:12px;font-weight:700;margin-bottom:6px;color:#111;}
        .terms-block p,.terms-block div,.terms-block li{margin:0 0 3px 0;padding:0;line-height:1.35;}
        .terms-block p:last-child,.terms-block li:last-child{margin-bottom:0;}
        .terms-block ul,.terms-block ol{margin:0 0 6px 0;padding-left:16px;}
        .terms-block ul li,.terms-block ol li{margin-bottom:2px;}
        .terms-block h1,.terms-block h2,.terms-block h3{margin:0 0 3px 0;padding:0;font-size:11px;line-height:1.35;font-weight:700;}
        .signature-label{font-size:11px;font-weight:600;color:#444;display:block;margin-bottom:4px;}
        .signature-line{margin-top:4px;border-bottom:1px solid #222;width:220px;height:28px;}
        /** NEver change position of these classes as this is the footer note*/
        .footer-note{width:100%;position:absolute;bottom:10px;margin-top:16px;padding-top:8px;text-align:center;font-size:11px;color:#666;border-top:1px solid #ddd;}
        .page-break-avoid{page-break-inside:avoid;}
    </style>
</head>
<body>
    <div class="header page-break-avoid">
        <table class="header-table">
            <tr>
                <td class="col-left">
                    <h1 class="invoice-title">{{ $L['tax_invoice'] }}</h1>
                </td>
                <td class="col-right">
                    @if($company)
                        <h2 class="company-name">{{ $company->name }}</h2>
                        @if($company->address)
                            <p class="header-meta" style="text-align:right">{!! \Str::replace("\n","<br>", $company->address) !!}</p>
                        @endif
                        @if($company->phone)
                            <p class="header-meta" style="text-align:right">Phone: {{ $company->phone }}</p>
                        @endif
                        @if($company->email)
                            <p class="header-meta" style="text-align:right">Email: {{ $company->email }}</p>
                        @endif
                        @if($company->gst_number)
                            <p class="header-meta" style="text-align:right"><strong>GST:</strong> {{ $company->gst_number }}</p>
                        @endif
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section page-break-avoid">
        <table class="details-table">
            <tr>
                <td class="col-left">
                    <h3 class="section-label">{{ $L['bill_to'] }}</h3>
                    <p class="section-value"><strong>{{ $invoice->customer->name }}</strong></p>
                    @if($invoice->customer->address)
                        <p class="section-value">{{ $invoice->customer->address }}</p>
                    @endif
                    @if($invoice->customer->phone)
                        <p class="section-value">Phone: {{ $invoice->customer->phone }}</p>
                    @endif
                    @if($invoice->customer->email)
                        <p class="section-value">Email: {{ $invoice->customer->email }}</p>
                    @endif
                    @if($invoice->customer->gst_number)
                        <p class="section-value"><strong>GST:</strong> {{ $invoice->customer->gst_number }}</p>
                    @endif
                </td>
                <td class="col-right">
                    <p class="section-value" style="text-align:right"><strong>{{ $L['invoice_no'] }}</strong> {{ $invoice->invoice_number }}</p>
                    <p class="section-value" style="text-align:right"><strong>{{ $L['date'] }}</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
                    <p class="section-value" style="text-align:right"><strong>{{ $L['payment_mode'] }}</strong> {{ $invoice->payment_mode }}</p>
                    @if($invoice->due_date)
                        <p class="section-value" style="text-align:right"><strong>{{ $L['due'] }}</strong> {{ $invoice->due_date->format('d M Y') }}</p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="section page-break-avoid">
        <table class="items">
            <thead>
                <tr>
                    <th class="cell-nowrap">#</th>
                    <th class="cell-product">{{ $L['product'] }}</th>
                    <th class="cell-nowrap">{{ $L['qty'] }}</th>
                    <th class="cell-nowrap text-right">{{ $L['gst_rate'] }}</th>
                    <th class="cell-nowrap text-right">{{ $L['price'] }}</th>
                    <th class="cell-nowrap text-right">CGST</th>
                    <th class="cell-nowrap text-right">SGST</th>
                    <th class="cell-nowrap text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td class="cell-nowrap">{{ $index + 1 }}</td>
                    <td class="cell-product">
                        <span class="item-product-name">{{ $item->product->name }}</span>
                        <div class="item-extra">
                            @if($item->product->hsn_code)
                                {{ $L['hsn'] }} {{ $item->product->hsn_code }}<br>
                            @endif
                            @if($item->serial_no)
                                {{ $L['serial_no'] }} {{ $item->serial_no }}<br>
                            @endif
                            @if($item->warranty_years !== null && $item->warranty_years !== '')
                                {{ $L['warranty'] }} {{ $item->warranty_years }} {{ $L['years'] }}<br>
                            @endif
                            @if($item->custom_short_text)
                                {!! nl2br(e($item->custom_short_text)) !!}
                            @endif
                        </div>
                    </td>
                    <td class="cell-nowrap">{{ $item->quantity }}</td>
                    <td class="cell-nowrap text-right">{{ $item->gst_rate }}%</td>
                    <td class="cell-nowrap text-right">₹ {{ number_format($item->taxable_amount, 2) }}</td>
                    <td class="cell-nowrap text-right">₹ {{ number_format($item->cgst_amount, 2) }}</td>
                    <td class="cell-nowrap text-right">₹ {{ number_format($item->sgst_amount, 2) }}</td>
                    <td class="cell-nowrap text-right">₹ {{ number_format($item->total_amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total-section page-break-avoid">
        <table class="total-table">
            <tr>
                <td>{{ $L['subtotal'] }}</td>
                <td>₹ {{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td>{{ $L['total_gst'] }}</td>
                <td>₹ {{ number_format($invoice->total_gst, 2) }}</td>
            </tr>
            @if($invoice->discount_amount > 0)
            <tr>
                <td>{{ $L['discount'] }}</td>
                <td>- ₹ {{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>{{ $L['grand_total'] }}</td>
                <td>₹ {{ number_format($invoice->grand_total, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($company && $company->invoice_terms_and_conditions)
    <div class="terms-block page-break-avoid">
        <div class="terms-heading">{{ $L['terms'] }}</div>
        {!! $company->invoice_terms_and_conditions !!}
    </div>
    @endif

    <div class="page-break-avoid" style="margin-top:16px;">
        <span class="signature-label">{{ $L['customer_signature'] }}</span>
        <div class="signature-line"></div>
    </div>

    @if($invoice->notes)
    <div style="margin-top:12px;clear:both;">
        <strong class="section-label" style="text-transform:none;">{{ $L['notes'] }}</strong>
        <p class="section-value" style="margin-top:2px;">{{ $invoice->notes }}</p>
    </div>
    @endif
    <div class="footer-note">
        <p style="margin:0;">Computer Generated Digital Invoice</p>
    </div>
</body>
</html>
