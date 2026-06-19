<?php

namespace App\Services;

use App\Models\InventoryReport;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\{FilePrintConnector, NetworkPrintConnector, WindowsPrintConnector};
use Carbon\Carbon;
use Exception;

/**
 * Service untuk mencetak laporan inventory ke thermal printer 58mm
 * Menggunakan library ESC/POS.
 */
class InventoryPrintService
{
    protected ?Printer $printer = null;

    /**
     * Membuka koneksi ke printer.
     *
     * @param string $connectionType 'windows'|'network'|'file'
     * @param string $target 
     *   - windows: nama printer (contoh: "EPSON TM-T20")
     *   - network: IP:port (contoh: "192.168.1.100:9100")
     *   - file: path file atau perangkat (contoh: "/dev/usb/lp0")
     * @return void
     * @throws Exception
     */
    public function connect(string $connectionType, string $target): void
    {
        try {
            switch ($connectionType) {
                case 'windows':
                    $connector = new WindowsPrintConnector($target);
                    break;
                case 'network':
                    [$ip, $port] = explode(':', $target);
                    $connector = new NetworkPrintConnector($ip, $port);
                    break;
                case 'file':
                    $connector = new FilePrintConnector($target);
                    break;
                default:
                    throw new Exception('Tipe koneksi tidak didukung: ' . $connectionType);
            }

            $this->printer = new Printer($connector);
        } catch (Exception $e) {
            throw new Exception('Gagal terhubung ke printer: ' . $e->getMessage());
        }
    }

    /**
     * Cetak satu laporan harian.
     *
     * @param int $reportId
     * @return array ['success' => bool, 'message' => string]
     */
    public function printDailyReport(int $reportId): array
    {
        $report = InventoryReport::with(['period.shift', 'items.productVariant'])
            ->find($reportId);

        if (!$report) {
            return ['success' => false, 'message' => 'Laporan tidak ditemukan.'];
        }

        if (!$this->printer) {
            return ['success' => false, 'message' => 'Printer belum terhubung. Panggil connect() terlebih dahulu.'];
        }

        try {
            $this->printHeader($report);
            $this->printItems($report->items);
            $this->printFooter($report);

            $this->printer->cut();
            $this->printer->close();

            return ['success' => true, 'message' => 'Laporan berhasil dicetak.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal mencetak: ' . $e->getMessage()];
        }
    }

    /**
     * Cetak laporan agregat (weekly/monthly).
     *
     * @param array $data Hasil dari InventoryReportService::getAggregatedReport()
     * @param string $type 'weekly'|'monthly'
     * @param string|null $date
     * @return array
     */
    public function printAggregatedReport(array $data, string $type, ?string $date = null): array
    {
        if (!$this->printer) {
            return ['success' => false, 'message' => 'Printer belum terhubung. Panggil connect() terlebih dahulu.'];
        }

        if (empty($data)) {
            return ['success' => false, 'message' => 'Tidak ada data untuk dicetak.'];
        }

        try {
            $p = $this->printer;

            // Header agregat
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->setEmphasis(true);
            $label = $type === 'weekly' ? 'LAPORAN MINGGUAN' : 'LAPORAN BULANAN';
            $p->text($label . "\n");
            $p->setEmphasis(false);
            $periodLabel = $date ? Carbon::parse($date)->format('d/m/Y') : now()->format('d/m/Y');
            $p->text("Periode: {$periodLabel}\n");
            $p->text(str_repeat('-', 32) . "\n");

            // Body agregat
            $p->setJustification(Printer::JUSTIFY_LEFT);
            $p->setEmphasis(true);
            $p->text("Produk   Awal Masuk Jual Sisa\n");
            $p->setEmphasis(false);
            $p->text(str_repeat('-', 32) . "\n");

            foreach ($data as $item) {
                $name = $item['product_variant']->variant_name ?? '-';
                $name = strlen($name) > 12 ? substr($name, 0, 10) . '..' : $name;

                $line = sprintf(
                    "%-12s %6s %5s %5s %5s\n",
                    $name,
                    number_format($item['first_stock'], 0),
                    number_format($item['stock_in'], 0),
                    number_format($item['selling'], 0),
                    number_format($item['remain'], 0)
                );
                $p->text($line);
            }

            $p->text(str_repeat('-', 32) . "\n");
            $p->setJustification(Printer::JUSTIFY_CENTER);
            $p->text("Dicetak: " . now()->format('d/m/Y H:i') . "\n");
            $p->text("Terima kasih\n");

            $p->cut();
            $p->close();

            return ['success' => true, 'message' => 'Laporan agregat berhasil dicetak.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Gagal mencetak: ' . $e->getMessage()];
        }
    }

    // ============== METODE PRIVATE ==============

    /**
     * Cetak header laporan harian
     */
    private function printHeader(InventoryReport $report): void
    {
        $p = $this->printer;

        $p->setJustification(Printer::JUSTIFY_CENTER);
        $p->setEmphasis(true);
        $p->text("LAPORAN INVENTORY\n");
        $p->setEmphasis(false);
        $p->text(str_repeat('-', 32) . "\n");

        $p->setJustification(Printer::JUSTIFY_LEFT);
        $p->text("Lokasi   : {$report->location}\n");
        $p->text("Tanggal  : {$report->report_date}\n");
        $p->text("Shift    : " . ($report->period->shift->name ?? '-') . "\n");
        $p->text("Kasir    : {$report->cashier_name}\n");
        $p->text("Pelapor  : {$report->reported_by}\n");
        $p->text("Total Terjual: {$report->total_products_sold}\n");
        $p->text(str_repeat('-', 32) . "\n");
    }

    /**
     * Cetak daftar item produk (tanpa kolom pengeluaran)
     */
    private function printItems($items): void
    {
        $p = $this->printer;

        $p->setEmphasis(true);
        $p->text("Produk   Awal Masuk Jual Sisa\n");
        $p->setEmphasis(false);
        $p->text(str_repeat('-', 32) . "\n");

        foreach ($items as $item) {
            $name = $item->productVariant->variant_name ?? '-';
            $name = strlen($name) > 12 ? substr($name, 0, 10) . '..' : $name;

            $line = sprintf(
                "%-12s %6s %5s %5s %5s\n",
                $name,
                number_format($item->first_stock, 0),
                number_format($item->stock_in, 0),
                number_format($item->selling, 0),
                number_format($item->remain, 0)
            );
            $p->text($line);
        }

        $p->text(str_repeat('-', 32) . "\n");
    }

    /**
     * Cetak footer
     */
    private function printFooter(InventoryReport $report): void
    {
        $p = $this->printer;
        $p->setJustification(Printer::JUSTIFY_CENTER);
        $p->text(str_repeat('=', 32) . "\n");
        $p->text("Dicetak: " . now()->format('d/m/Y H:i') . "\n");
        $p->text("Terima kasih\n");
        $p->setJustification(Printer::JUSTIFY_LEFT);
    }
}