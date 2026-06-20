<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Inventory Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            padding: 20px;
            font-family: 'Courier New', monospace;
        }
        .receipt {
            width: 100%;
            max-width: 350px; /* 58mm ~ 350px */
            background: #fff;
            padding: 16px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .dashed { border-top: 1px dashed #333; margin: 8px 0; }
        .solid { border-top: 2px solid #333; margin: 8px 0; }
        .mt-1 { margin-top: 6px; }
        .mb-1 { margin-bottom: 6px; }
        .row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            line-height: 1.6;
        }
        .row-header {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 1px dotted #999;
            margin-bottom: 4px;
            padding-bottom: 4px;
        }
        .item-name {
            width: 40%;
        }
        .item-qty {
            width: 15%;
            text-align: right;
        }
        .item-col {
            width: 15%;
            text-align: right;
        }
        .flex {
            display: flex;
            justify-content: space-between;
        }
        .print-btn {
            margin-top: 16px;
            text-align: center;
        }
        .print-btn button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        .print-btn button:hover {
            background: #0056b3;
        }
        /* Untuk layar kecil dan print */
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { max-width: 100%; box-shadow: none; border-radius: 0; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt" id="receipt">
        <!-- HEADER -->
        <div class="text-center bold" style="font-size:16px; margin-bottom:4px;">
            Inventory Report
        </div>
        <div class="dashed"></div>

        @if(isset($report) && !isset($isAggregated))
            {{-- ==================== LAPORAN HARIAN (DAILY) ==================== --}}
            <div class="row"><span>Location</span><span>{{ $report->location }}</span></div>
            <div class="row"><span>Date</span><span>{{ $report->report_date }}</span></div>
            <div class="row"><span>Shift</span><span>{{ $report->period->shift->name ?? '-' }}</span></div>
            <div class="row"><span>Cashier</span><span>{{ $report->cashier_name }}</span></div>
            <div class="row"><span>Report By</span><span>{{ $report->reported_by }}</span></div>
            <div class="row"><span>Total Product Sold</span><span>{{ number_format($report->total_products_sold, 0) }}</span></div>
            <div class="dashed"></div>

            <div class="row row-header">
                <span class="item-name">Product</span>
                <span class="item-qty">First Stock</span>
                <span class="item-qty">Stock In</span>
                <span class="item-qty">Selling</span>
                <span class="item-qty">Remain</span>
            </div>

            @foreach($report->items as $item)
                <div class="row" style="font-size:12px;">
                    <span class="item-name">{{ Str::limit($item->productVariant->variant_name ?? '-', 14) }}</span>
                    <span class="item-qty">{{ number_format($item->first_stock, 0) }}</span>
                    <span class="item-qty">{{ number_format($item->stock_in, 0) }}</span>
                    <span class="item-qty">{{ number_format($item->selling, 0) }}</span>
                    <span class="item-qty">{{ number_format($item->remain, 0) }}</span>
                </div>
            @endforeach

            <div class="dashed"></div>
            <div class="text-center" style="font-size:11px; margin-top:6px;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>

        @else
            {{-- ==================== LAPORAN MINGGUAN / BULANAN (WEEKLY / MONTHLY) ==================== --}}
            <div class="text-center bold" style="font-size:14px;">
                {{ strtoupper($type) === 'WEEKLY' ? 'Weekly Report' : 'Monthly Report' }}
            </div>
            <div class="row">
                <span>Period</span>
                <span>{{ $date ? \Carbon\Carbon::parse($date)->format('d/m/Y') : now()->format('d/m/Y') }}</span>
            </div>
            <div class="dashed"></div>

            <div class="row row-header">
                <span class="item-name">Product</span>
                <span class="item-qty">First Stock</span>
                <span class="item-qty">Stock In</span>
                <span class="item-qty">Selling</span>
                <span class="item-qty">Remain</span>
            </div>

            @forelse($data as $item)
                <div class="row" style="font-size:12px;">
                    <span class="item-name">{{ Str::limit($item['product_variant']->variant_name ?? '-', 14) }}</span>
                    <span class="item-qty">{{ number_format($item['first_stock'], 0) }}</span>
                    <span class="item-qty">{{ number_format($item['stock_in'], 0) }}</span>
                    <span class="item-qty">{{ number_format($item['selling'], 0) }}</span>
                    <span class="item-qty">{{ number_format($item['remain'], 0) }}</span>
                </div>
            @empty
                <div class="text-center" style="margin:8px 0;">Data not found</div>
            @endforelse

            <div class="dashed"></div>
            <div class="text-center" style="font-size:11px; margin-top:6px;">
                Dicetak: {{ now()->format('d/m/Y H:i') }}
            </div>
        @endif

        <div class="text-center bold" style="margin-top:10px;">Terima kasih</div>

        <!-- Tombol cetak fisik (tampil hanya di layar) -->
        <div class="print-btn">
            <button onclick="window.print()">🖨️ Print</button>
        </div>
    </div>
</body>
</html>