<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $quote->reference }} — Quote</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1e293b; font-size: 14px; line-height: 1.5; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #e2e8f0; }
        .company-name { font-size: 24px; font-weight: 700; color: #0f172a; }
        .company-sub { font-size: 12px; color: #64748b; margin-top: 4px; }
        .quote-meta { text-align: right; }
        .quote-ref { font-size: 20px; font-weight: 700; color: #2563eb; }
        .quote-date { font-size: 12px; color: #64748b; margin-top: 4px; }
        .status { display: inline-block; padding: 2px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-draft { background: #f1f5f9; color: #64748b; }
        .status-sent { background: #dbeafe; color: #2563eb; }
        .status-accepted { background: #dcfce7; color: #16a34a; }
        .client-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .client-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 6px; }
        .client-name { font-size: 16px; font-weight: 600; color: #0f172a; }
        .client-company { font-size: 13px; color: #64748b; }
        .section-title { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 12px; }
        .scope { margin-bottom: 30px; white-space: pre-line; color: #334155; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { text-align: left; padding: 8px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; border-bottom: 2px solid #e2e8f0; }
        th.right { text-align: right; }
        th.center { text-align: center; }
        td { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #334155; }
        td.right { text-align: right; }
        td.center { text-align: center; }
        .totals { margin-left: auto; width: 280px; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #64748b; }
        .totals-row.total { border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 4px; font-size: 18px; font-weight: 700; color: #0f172a; }
        .terms { margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0; }
        .terms p { font-size: 12px; color: #64748b; white-space: pre-line; }
        .footer { margin-top: 60px; padding-top: 20px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8; }
        .valid-until { font-size: 12px; color: #64748b; margin-top: 8px; }
        @media print {
            body { padding: 20px; }
            @page { margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="company-name">{{ $tenant->name }}</div>
            <div class="company-sub">Engineering Consultancy</div>
        </div>
        <div class="quote-meta">
            <div class="quote-ref">{{ $quote->reference }}</div>
            <div class="quote-date">{{ $quote->created_at->format('F d, Y') }}</div>
            <div style="margin-top: 6px;"><span class="status status-{{ $quote->status }}">{{ ucfirst($quote->status) }}</span></div>
        </div>
    </div>

    <div class="client-box">
        <div class="client-label">Prepared for</div>
        <div class="client-name">{{ $quote->client_name }}</div>
        @if($quote->client_company)
        <div class="client-company">{{ $quote->client_company }}</div>
        @endif
    </div>

    @if($quote->scope_of_work)
    <div style="margin-bottom: 30px;">
        <div class="section-title">Scope of Work</div>
        <div class="scope">{{ $quote->scope_of_work }}</div>
    </div>
    @endif

    @if($quote->lineItems->count())
    <div class="section-title">Line Items</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="center">Qty</th>
                <th class="center">Unit</th>
                <th class="right">Rate</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quote->lineItems as $li)
            <tr>
                <td>{{ $li->description }}</td>
                <td class="center">{{ number_format($li->quantity, 2) }}</td>
                <td class="center" style="text-transform:capitalize;">{{ $li->unit }}</td>
                <td class="right">${{ number_format($li->rate, 2) }}</td>
                <td class="right" style="font-weight:600;">${{ number_format($li->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row"><span>Subtotal</span><span>${{ number_format($quote->subtotal, 2) }}</span></div>
        <div class="totals-row"><span>HST ({{ number_format($quote->tax_rate, 0) }}%)</span><span>${{ number_format($quote->tax_amount, 2) }}</span></div>
        <div class="totals-row total"><span>Total</span><span>${{ number_format($quote->total, 2) }}</span></div>
    </div>
    @endif

    @if($quote->valid_until)
    <div class="valid-until">This quote is valid until <strong>{{ $quote->valid_until->format('F d, Y') }}</strong>.</div>
    @endif

    @if($quote->terms)
    <div class="terms">
        <div class="section-title">Terms & Conditions</div>
        <p>{{ $quote->terms }}</p>
    </div>
    @endif

    <div class="footer">
        <p>{{ $tenant->name }} &middot; Generated {{ now()->format('F d, Y') }}</p>
    </div>
</body>
</html>
