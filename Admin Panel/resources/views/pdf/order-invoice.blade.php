<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #2d3436; background: #fff; }

        .page { padding: 40px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .brand-name { font-size: 28px; font-weight: 700; color: #2d6a4f; letter-spacing: -0.5px; }
        .brand-tagline { font-size: 11px; color: #74b49b; margin-top: 2px; }
        .invoice-meta { text-align: right; }
        .invoice-title { font-size: 22px; font-weight: 700; color: #2d6a4f; }
        .invoice-number { font-size: 12px; color: #636e72; margin-top: 4px; }

        /* Divider */
        .divider { border: none; border-top: 2px solid #2d6a4f; margin: 0 0 30px; }

        /* Info Grid */
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 20px; }
        .info-block { flex: 1; }
        .info-label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #74b49b; letter-spacing: 0.5px; margin-bottom: 6px; }
        .info-value { font-size: 13px; line-height: 1.6; }
        .info-value strong { color: #2d3436; }

        /* Status badge */
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .status-delivered { background: #d8f3dc; color: #1b4332; }
        .status-shipped   { background: #e8f4fd; color: #1e3a5f; }
        .status-processing { background: #fff9db; color: #7c5c00; }
        .status-completed  { background: #d8f3dc; color: #1b4332; }

        /* Items Table */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .items-table thead tr { background: #2d6a4f; color: #fff; }
        .items-table thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        .items-table tbody tr { border-bottom: 1px solid #f0f0f0; }
        .items-table tbody tr:nth-child(even) { background: #f8fffe; }
        .items-table tbody td { padding: 10px 12px; vertical-align: top; }
        .items-table .text-right { text-align: right; }
        .items-table .text-center { text-align: center; }
        .product-name { font-weight: 600; }
        .product-sku { font-size: 10px; color: #b2bec3; margin-top: 2px; }

        /* Totals */
        .totals-section { display: flex; justify-content: flex-end; margin-bottom: 30px; }
        .totals-table { width: 300px; }
        .totals-table tr td { padding: 5px 0; font-size: 13px; }
        .totals-table .label { color: #636e72; }
        .totals-table .value { text-align: right; font-weight: 500; }
        .totals-table .grand-total td { border-top: 2px solid #2d6a4f; padding-top: 10px; font-size: 16px; font-weight: 700; color: #2d6a4f; }

        /* Footer */
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; color: #b2bec3; font-size: 11px; line-height: 1.8; }

        /* Watermark for paid */
        @if(in_array($order->status, ['delivered','completed']))
        .paid-stamp { position: fixed; top: 40%; left: 30%; opacity: 0.06; font-size: 100px; font-weight: 900; color: #2d6a4f; transform: rotate(-30deg); pointer-events: none; z-index: 0; }
        @endif
    </style>
</head>
<body>
<div class="page">

    @if(in_array($order->status, ['delivered','completed']))
    <div class="paid-stamp">PAID</div>
    @endif

    {{-- ── Header ── --}}
    <div class="header">
        <div>
            <div class="brand-name">🌿 Plantix AI</div>
            <div class="brand-tagline">Smart Agriculture Platform</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#INV-{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
            <div class="invoice-number">Date: {{ $order->created_at->format('d M Y') }}</div>
        </div>
    </div>

    <hr class="divider">

    {{-- ── Billing & Shipping Info ── --}}
    <div class="info-grid">
        <div class="info-block">
            <div class="info-label">Billed To</div>
            <div class="info-value">
                <strong>{{ optional($order->user)->name }}</strong><br>
                {{ optional($order->user)->email }}<br>
                {{ optional($order->user)->phone ?? '' }}
            </div>
        </div>
        <div class="info-block">
            <div class="info-label">Delivery Address</div>
            <div class="info-value">
                {!! nl2br(e($order->delivery_address ?? 'N/A')) !!}
            </div>
        </div>
        <div class="info-block">
            <div class="info-label">Vendor / Store</div>
            <div class="info-value">
                <strong>{{ optional($order->vendor)->store_name ?? 'Plantix Store' }}</strong><br>
                {{ optional($order->vendor)->store_email ?? '' }}<br>
                {{ optional($order->vendor)->store_phone ?? '' }}
            </div>
        </div>
        <div class="info-block" style="text-align:right;">
            <div class="info-label">Order Status</div>
            <div class="info-value">
                <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
            @if($order->tracking_number)
            <div class="info-value" style="margin-top:8px;">
                <div class="info-label">Tracking #</div>
                {{ $order->tracking_number }}
            </div>
            @endif
        </div>
    </div>

    {{-- ── Order Items ── --}}
    <table class="items-table">
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="45%">Product</th>
                <th width="15%" class="text-center">Qty</th>
                <th width="17%" class="text-right">Unit Price</th>
                <th width="18%" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    <div class="product-name">{{ optional($item->product)->name ?? 'Product #' . $item->product_id }}</div>
                    @if(optional($item->product)->sku)
                        <div class="product-sku">SKU: {{ $item->product->sku }}</div>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-right">PKR {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">PKR {{ number_format($item->unit_price * $item->quantity, 2) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:20px;color:#b2bec3;">No items.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Totals ── --}}
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td class="label">Subtotal</td>
                <td class="value">PKR {{ number_format($order->subtotal ?? 0, 2) }}</td>
            </tr>
            @if(($order->delivery_fee ?? 0) > 0)
            <tr>
                <td class="label">Delivery Fee</td>
                <td class="value">PKR {{ number_format($order->delivery_fee, 2) }}</td>
            </tr>
            @endif
            @if(($order->discount_amount ?? 0) > 0)
            <tr>
                <td class="label">Discount</td>
                <td class="value" style="color:#e17055;">- PKR {{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            @endif
            @if(($order->tax_amount ?? 0) > 0)
            <tr>
                <td class="label">Tax</td>
                <td class="value">PKR {{ number_format($order->tax_amount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>Total</td>
                <td>PKR {{ number_format($order->total ?? 0, 2) }}</td>
            </tr>
        </table>
    </div>

    {{-- ── Notes ── --}}
    @if($order->notes)
    <div style="background:#f8fffe;border-left:3px solid #2d6a4f;padding:12px 16px;border-radius:4px;margin-bottom:24px;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:#74b49b;margin-bottom:4px;">Order Notes</div>
        <div style="font-size:12px;color:#2d3436;">{{ $order->notes }}</div>
    </div>
    @endif

    {{-- ── Footer ── --}}
    <div class="footer">
        <div>Thank you for choosing <strong>Plantix AI</strong> — Smart Agriculture Platform.</div>
        <div>For queries, contact <strong>support@plantix.ai</strong> | Generated: {{ now()->format('d M Y, H:i') }}</div>
        <div style="margin-top:6px;font-size:10px;">This is a computer-generated invoice and does not require a physical signature.</div>
    </div>

</div>
</body>
</html>
