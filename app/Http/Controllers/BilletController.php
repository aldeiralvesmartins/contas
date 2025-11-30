<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BilletController extends Controller
{
    public function uploadBillet(Request $request)
    {
        $request->validate([
            'billet' => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:5120'
        ]);

        try {
            $file = $request->file('billet');
            $fileExtension = $file->getClientOriginalExtension();

            // Processar o arquivo baseado no tipo
            if ($fileExtension === 'pdf') {
                $data = $this->processPdf($file);
            } else {
                $data = $this->processImage($file);
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Boleto processado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar boleto: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processPdf($file)
    {
        // Salvar temporariamente o PDF
        $tempPath = $file->store('temp', 'local');
        $fullPath = storage_path('app/' . $tempPath);

        try {
            // Extrair texto do PDF
            $text = (new Pdf())
                ->setPdf($fullPath)
                ->text();

            // Processar o texto extraído
            $data = $this->extractBilletData($text);

            // Limpar arquivo temporário
            Storage::delete($tempPath);

            return $data;

        } catch (\Exception $e) {
            Storage::delete($tempPath);
            throw new \Exception('Erro ao extrair texto do PDF: ' . $e->getMessage());
        }
    }

    private function processImage($file)
    {
        // Processar imagem (futuramente integrar com API de OCR)
        $data = [
            'title' => $this->extractTitleFromFilename($file->getClientOriginalName()),
            'amount' => null,
            'due_date' => null,
            'barcode' => null,
            'type' => 'image_upload'
        ];

        // Aqui você pode integrar com uma API de OCR como:
        // Google Vision API, Azure Computer Vision, Tesseract OCR
        // Por enquanto, vamos apenas extrair do nome do arquivo

        return $data;
    }

    private function extractBilletData($text)
    {
        $data = [
            'title' => $this->extractTitle($text),
            'amount' => $this->extractAmount($text),
            'due_date' => $this->extractDueDate($text),
            'barcode' => $this->extractBarcode($text),
            'type' => 'billet'
        ];

        return $data;
    }

    private function extractTitle($text)
    {
        // Padrões comuns em boletos
        $patterns = [
            '/CONTA DE ENERGIA.*?\n(.+?)\n/i',
            '/CONTA DE ÁGUA.*?\n(.+?)\n/i',
            '/CONTA DE LUZ.*?\n(.+?)\n/i',
            '/FATURA.*?\n(.+?)\n/i',
            '/BOLETO.*?\n(.+?)\n/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return trim($matches[1]);
            }
        }

        // Tentar extrair do início do texto
        $lines = explode("\n", $text);
        foreach ($lines as $line) {
            if (strlen(trim($line)) > 5 && strlen(trim($line)) < 100) {
                return trim($line);
            }
        }

        return 'Conta Importada';
    }

    private function extractAmount($text)
    {
        // Padrões para valor
        $patterns = [
            '/R\$\s*([0-9.,]+)/i',
            '/VALOR.*?R\$\s*([0-9.,]+)/i',
            '/TOTAL.*?R\$\s*([0-9.,]+)/i',
            '/([0-9]{1,3}(?:\.[0-9]{3})*,[0-9]{2})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $amount = str_replace(['.', ','], ['', '.'], $matches[1]);
                return floatval($amount);
            }
        }

        return null;
    }

    private function extractDueDate($text)
    {
        // Padrões para data de vencimento
        $patterns = [
            '/VENCIMENTO.*?([0-9]{2}\/[0-9]{2}\/[0-9]{4})/i',
            '/VENC\.*?([0-9]{2}\/[0-9]{2}\/[0-9]{4})/i',
            '/([0-9]{2}\/[0-9]{2}\/[0-9]{4})/',
            '/([0-9]{4}-[0-9]{2}-[0-9]{2})/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $date = $matches[1];

                // Converter para formato Y-m-d
                if (strpos($date, '/') !== false) {
                    $date = \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
                }

                return $date;
            }
        }

        return null;
    }

    private function extractBarcode($text)
    {
        // Extrair código de barras (linha digitável)
        $patterns = [
            '/[0-9]{5}\.[0-9]{5}\s+[0-9]{5}\.[0-9]{6}\s+[0-9]{5}\.[0-9]{6}\s+[0-9]\s+[0-9]{14}/',
            '/[0-9]{47}/',
            '/[0-9]{44}/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                return preg_replace('/[^0-9]/', '', $matches[0]);
            }
        }

        return null;
    }

    private function extractTitleFromFilename($filename)
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Remover números e caracteres especiais
        $name = preg_replace('/[0-9_-]/', ' ', $name);
        $name = trim($name);

        if (empty($name)) {
            return 'Conta Importada';
        }

        return ucwords($name);
    }
}
