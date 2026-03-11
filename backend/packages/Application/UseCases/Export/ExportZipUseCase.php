<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Export;

use Packages\Application\DTOs\PhotoDto;

final class ExportZipUseCase
{
    public function __construct(
        private readonly ExportItineraryPdfUseCase $itineraryPdfUseCase,
        private readonly ExportPhotobookUseCase $photobookUseCase,
    ) {}

    /**
     * しおりデータと写真一覧を取得する。
     *
     * @return array{itinerary: array{items: array<string, list<array<string, mixed>>>, spots: array<int, string>}, photos: PhotoDto[]}
     */
    public function execute(): array
    {
        return [
            'itinerary' => $this->itineraryPdfUseCase->execute(),
            'photos' => $this->photobookUseCase->execute(),
        ];
    }
}
