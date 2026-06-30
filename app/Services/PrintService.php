<?php

namespace App\Services;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\UsbPrintConnector;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class PrintService
{
    protected $printer;

    /**
     * Konfigurasi printer.
     * Bisa disimpan di .env atau config.
     */
    protected $printerType = 'network'; // network, windows, usb, file
    protected $printerAddress = '192.168.1.100'; // IP untuk network
    protected $printerPort = 9100;
    protected $printerName = 'POS-58'; // untuk windows share name
    protected $usbVendorId = 0x0483; // untuk USB (opsional)
    protected $usbProductId = 0x5750; // untuk USB (opsional)
    protected $filePath = '/dev/usb/lp0'; // untuk file (Linux)

    public function __construct()
    {
        // Bisa baca dari config/print.php atau .env
        $this->printerType = env('PRINTER_TYPE', 'network');
        $this->printerAddress = env('PRINTER_IP', '192.168.1.100');
        $this->printerPort = env('PRINTER_PORT', 9100);
        $this->printerName = env('PRINTER_NAME', 'POS-58');
        $this->filePath = env('PRINTER_FILE', '/dev/usb/lp0');
    }

    /**
     * Membuka koneksi ke printer.
     */
    protected function connect()
    {
        try {
            switch ($this->printerType) {
                case 'network':
                    $connector = new NetworkPrintConnector($this->printerAddress, $this->printerPort);
                    break;
                case 'windows':
                    $connector = new WindowsPrintConnector($this->printerName);
                    break;
                case 'usb':
                    $connector = new UsbPrintConnector($this->usbVendorId, $this->usbProductId);
                    break;
                case 'file':
                    $connector = new FilePrintConnector($this->filePath);
                    break;
                default:
                    throw new \Exception('Printer type not supported');
            }
            $this->printer = new Printer($connector);
            return true;
        } catch (\Exception $e) {
            Log::error('Printer connection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cetak struk transaksi.
     */
    public function printReceipt(Transaction $transaction)
    {
        try {
            $this->connect();

            $printer = $this->printer;

            // Inisialisasi printer
            $printer->initialize();

            // --- Header ---
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text($transaction->company->company_name ?? 'My Fried Chicken');
            $printer->selectPrintMode(Printer::MODE_FONT_B);
            $printer->text("\n" . ($transaction->company->address ?? 'Pusat') . "\n");
            $printer->text("Telp: " . ($transaction->company->phone ?? '') . "\n");
            $printer->text(str_repeat('-', 32) . "\n");

            // --- Info transaksi ---
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("No. : " . $transaction->transaction_number . "\n");
            $printer->text("Kasir : " . ($transaction->user->name ?? 'Guest') . "\n");
            $printer->text("Tanggal : " . $transaction->formatted_date . "\n");
            $printer->text("Metode : " . ucfirst($transaction->payment_method) . "\n");
            $printer->text(str_repeat('-', 32) . "\n");

            // --- Item ---
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Item               Qty  Total\n");
            $printer->text(str_repeat('-', 32) . "\n");
            foreach ($transaction->items as $item) {
                $name = substr($item->name, 0, 18);
                $qty = str_pad($item->qty, 3, ' ', STR_PAD_LEFT);
                $subtotal = number_format($item->subtotal, 0, ',', '.');
                $printer->text(sprintf("%-18s %3s  %s\n", $name, $qty, $subtotal));
            }
            $printer->text(str_repeat('-', 32) . "\n");

            // --- Total ---
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            if ($transaction->discount_amount > 0) {
                $printer->text("Subtotal : " . number_format($transaction->subtotal, 0, ',', '.') . "\n");
                $printer->text("Diskon   : -" . number_format($transaction->discount_amount, 0, ',', '.') . "\n");
            }
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("TOTAL    : " . number_format($transaction->total, 0, ',', '.') . "\n");
            $printer->selectPrintMode(Printer::MODE_FONT_B);
            $printer->text("Bayar    : " . number_format($transaction->paid, 0, ',', '.') . "\n");
            $printer->text("Kembali  : " . number_format($transaction->change, 0, ',', '.') . "\n");
            $printer->text(str_repeat('-', 32) . "\n");

            // --- Footer ---
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Terima kasih\n");
            $printer->text("Powered by KitaPOS\n");
            $printer->text("\n\n\n");

            // Potong kertas
            $printer->cut();

            // Tutup koneksi
            $printer->close();

            return true;
        } catch (\Exception $e) {
            Log::error('Print error: ' . $e->getMessage());
            throw $e;
        }
    }
}