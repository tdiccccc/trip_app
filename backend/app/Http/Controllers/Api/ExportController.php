<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Packages\Application\UseCases\Export\ExportItineraryPdfUseCase;
use Packages\Application\UseCases\Export\ExportPhotobookUseCase;
use Packages\Application\UseCases\Export\ExportZipUseCase;
use ZipArchive;

final class ExportController extends Controller
{
    /**
     * POST /api/trips/{tripId}/export/itinerary-pdf
     */
    public function itineraryPdf(int $tripId, ExportItineraryPdfUseCase $useCase): Response
    {
        $data = $useCase->execute($tripId);

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = Pdf::loadView('exports.itinerary', [
            'items' => $data['items'],
            'spots' => $data['spots'],
        ]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="itinerary.pdf"',
        ]);
    }

    /**
     * POST /api/trips/{tripId}/export/photobook-pdf
     */
    public function photobookPdf(int $tripId, ExportPhotobookUseCase $useCase): Response
    {
        $photos = $useCase->execute($tripId);

        /** @var \Barryvdh\DomPDF\PDF $pdf */
        $pdf = Pdf::loadView('exports.photobook', [
            'photos' => $photos,
        ]);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="photobook.pdf"',
        ]);
    }

    /**
     * POST /api/trips/{tripId}/export/slideshow-video
     *
     * サーバーサイド動画生成は複雑なため、準備中スタブ実装。
     */
    public function slideshowVideo(int $tripId): JsonResponse
    {
        return response()->json([
            'message' => 'This feature is currently under development.',
        ], 501);
    }

    /**
     * POST /api/trips/{tripId}/export/zip
     */
    public function zip(int $tripId, ExportZipUseCase $useCase): Response
    {
        $data = $useCase->execute($tripId);

        // しおり PDF を生成
        /** @var \Barryvdh\DomPDF\PDF $itineraryPdf */
        $itineraryPdf = Pdf::loadView('exports.itinerary', [
            'items' => $data['itinerary']['items'],
            'spots' => $data['itinerary']['spots'],
        ]);

        // ZIP ファイルを作成
        $zipPath = tempnam(sys_get_temp_dir(), 'ise_export_').'.zip';
        $zip = new ZipArchive;
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // しおり PDF を追加
        $zip->addFromString('itinerary.pdf', $itineraryPdf->output());

        // 写真ファイルを追加
        foreach ($data['photos'] as $photo) {
            $fileContent = Storage::disk('s3')->get($photo->storagePath);
            if ($fileContent !== null) {
                $zip->addFromString('photos/'.$photo->originalFilename, $fileContent);
            }
        }

        $zip->close();

        $content = (string) file_get_contents($zipPath);
        unlink($zipPath);

        return response($content, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="ise-trip-export.zip"',
        ]);
    }
}
