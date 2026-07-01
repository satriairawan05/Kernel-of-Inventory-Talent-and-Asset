<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Gudang</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            padding: 20px;
            font-family: 'Courier New', monospace;
        }
        .receipt {
            width: 100%;
            max-width: 350px;
            background: #fff;
            padding: 16px 12px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed { border-top: 1px dashed #333; margin: 8px 0; }
        .solid { border-top: 2px solid #333; margin: 8px 0; }
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
        .item-name { width: 40%; }
        .item-qty { width: 15%; text-align: right; }
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
        .print-btn button:hover { background: #0056b3; }
        .total-row {
            border-top: 1px double #333;
            padding-top: 4px;
            margin-top: 4px;
            font-weight: bold;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { max-width: 100%; box-shadow: none; border-radius: 0; }
            .print-btn { display: none; }
        }
        .label { font-weight: bold; }
        .value { font-weight: normal; }
    </style>
</head>
<body>
    <div class="receipt" id="receipt">

        <!-- HEADER PERUSAHAAN -->
        <div class="text-center bold" style="font-size:16px; margin-bottom:2px;">
            {{ session('company_name', config('app.company_name', 'My Fried Chicken')) }}
        </div>
        <div class="text-center" style="font-size:12px; margin-bottom:2px;">
            {{ session('company_address', config('app.company_address', 'Jl. Raya No. 1')) }}
        </div>
        <div class="text-center" style="font-size:12px; margin-bottom:6px;">
            Telp: {{ session('company_phone', config('app.company_phone', '-')) }}
        </div>
        <div class="dashed"></div>

        <!-- JUDUL LAPORAN -->
        <div class="text-center bold" style="font-size:15px; margin-bottom:4px;">
            LAPORAN STOK GUDANG
        </div>

        @if(isset($report) && !isset($isAggregated))
            {{-- ==================== LAPORAN HARIAN ==================== --}}
            <div class="row"><span class="label">Lokasi</span><span class="value">{{ $report->location ?? '-' }}</span></div>
            <div class="row"><span class="label">Tanggal</span><span class="value">{{ isset($report->report_date) ? \Carbon\Carbon::parse($report->report_date)->format('d/m/Y') : '-' }}</span></div>
            <div class="row"><span class="label">Shift</span><span class="value">{{ $report->period->shift->name ?? '-' }}</span></div>
            <div class="row"><span class="label">Pelapor</span><span class="value">{{ $report->reported_by ?? '-' }}</span></div>
            <div class="row"><span class="label">Total Terjual</span><span class="value">{{ number_format($report->total_products_sold ?? 0, 0) }}</span></div>
            <div class="dashed"></div>

            <div class="row row-header">
                <span class="item-name">Produk / Varian</span>
                <span class="item-qty">Awal</span>
                <span class="item-qty">Masuk</span>
                <span class="item-qty">Terjual</span>
                <span class="item-qty">Sisa</span>
            </div>

            @php
                $totalFirst = 0; $totalIn = 0; $totalSelling = 0; $totalRemain = 0;
            @endphp

            @forelse($report->items as $item)
                @php
                    $totalFirst += $item->first_stock ?? 0;
                    $totalIn += $item->stock_in ?? 0;
                    $totalSelling += $item->selling ?? 0;
                    $totalRemain += $item->remain ?? 0;
                @endphp
                <div class="row" style="font-size:12px;">
                    <span class="item-name">{{ Str::limit($item->productVariant->variant_name ?? '-', 14) }}</span>
                    <span class="item-qty">{{ number_format($item->first_stock ?? 0, 0) }}</span>
                    <span class="item-qty">{{ number_format($item->stock_in ?? 0, 0) }}</span>
                    <span class="item-qty">{{ number_format($item->selling ?? 0, 0) }}</span>
                    <span class="item-qty">{{ number_format($item->remain ?? 0, 0) }}</span>
                </div>
            @empty
                <div class="text-center" style="margin:8px 0;">Tidak ada item</div>
            @endforelse

            @if($report->items->count())
                <div class="dashed"></div>
                <div class="row total-row">
                    <span class="item-name">TOTAL</span>
                    <span class="item-qty">{{ number_format($totalFirst, 0) }}</span>
                    <span class="item-qty">{{ number_format($totalIn, 0) }}</span>
                    <span class="item-qty">{{ number_format($totalSelling, 0) }}</span>
                    <span class="item-qty">{{ number_format($totalRemain, 0) }}</span>
                </div>
            @endif

        @else
            {{-- ==================== LAPORAN MINGGUAN / BULANAN ==================== --}}
            <div class="text-center bold" style="font-size:14px;">
                {{ strtoupper($type ?? '') === 'WEEKLY' ? 'LAPORAN MINGGUAN' : 'LAPORAN BULANAN' }}
            </div>
            <div class="row">
                <span class="label">Periode</span>
                <span class="value">{{ isset($date) ? \Carbon\Carbon::parse($date)->format('d/m/Y') : now()->format('d/m/Y') }}</span>
            </div>
            <div class="dashed"></div>

            <div class="row row-header">
                <span class="item-name">Produk / Varian</span>
                <span class="item-qty">Awal</span>
                <span class="item-qty">Masuk</span>
                <span class="item-qty">Terjual</span>
                <span class="item-qty">Sisa</span>
            </div>

            @php
                $totalFirst = 0; $totalIn = 0; $totalSelling = 0; $totalRemain = 0;
            @endphp

            @forelse($data as $item)
                @php
                    $totalFirst += $item['first_stock'] ?? 0;
                    $totalIn += $item['stock_in'] ?? 0;
                    $totalSelling += $item['selling'] ?? 0;
                    $totalRemain += $item['remain'] ?? 0;
                @endphp
                <div class="row" style="font-size:12px;">
                    <span class="item-name">{{ Str::limit($item['product_variant']->variant_name ?? '-', 14) }}</span>
                    <span class="item-qty">{{ number_format($item['first_stock'] ?? 0, 0) }}</span>
                    <span class="item-qty">{{ number_format($item['stock_in'] ?? 0, 0) }}</span>
                    <span class="item-qty">{{ number_format($item['selling'] ?? 0, 0) }}</span>
                    <span class="item-qty">{{ number_format($item['remain'] ?? 0, 0) }}</span>
                </div>
            @empty
                <div class="text-center" style="margin:8px 0;">Data tidak ditemukan</div>
            @endforelse

            @if(!empty($data))
                <div class="dashed"></div>
                <div class="row total-row">
                    <span class="item-name">TOTAL</span>
                    <span class="item-qty">{{ number_format($totalFirst, 0) }}</span>
                    <span class="item-qty">{{ number_format($totalIn, 0) }}</span>
                    <span class="item-qty">{{ number_format($totalSelling, 0) }}</span>
                    <span class="item-qty">{{ number_format($totalRemain, 0) }}</span>
                </div>
            @endif
        @endif

        <div class="dashed"></div>
        <div class="text-center" style="font-size:11px; margin-top:6px;">
            Dicetak: {{ now()->format('d/m/Y H:i:s') }}
        </div>
        <div class="text-center bold" style="margin-top:8px;">Terima kasih</div>

        <!-- Tombol cetak preview -->
        <div class="print-btn">
            <button onclick="window.print()">🖨️ Cetak Laporan</button>
        </div>
    </div>
</body>
</html>