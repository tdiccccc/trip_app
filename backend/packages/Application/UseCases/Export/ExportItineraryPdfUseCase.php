<?php

declare(strict_types=1);

namespace Packages\Application\UseCases\Export;

use Packages\Application\DTOs\ItineraryItemDto;
use Packages\Domain\Repositories\ItineraryRepositoryInterface;
use Packages\Domain\Repositories\SpotRepositoryInterface;

final class ExportItineraryPdfUseCase
{
    public function __construct(
        private readonly ItineraryRepositoryInterface $itineraryRepository,
        private readonly SpotRepositoryInterface $spotRepository,
    ) {}

    /**
     * しおりデータを日付・時間順で取得し、スポット名も付与して返す。
     *
     * @return array{items: array<string, list<array<string, mixed>>>, spots: array<int, string>}
     */
    public function execute(): array
    {
        $items = $this->itineraryRepository->findAll();
        $dtos = array_map(
            fn ($item) => ItineraryItemDto::fromEntity($item),
            $items,
        );

        // スポット名のマップを構築
        $spotNames = [];
        foreach ($dtos as $dto) {
            if ($dto->spotId !== null && ! isset($spotNames[$dto->spotId])) {
                $spot = $this->spotRepository->findById($dto->spotId);
                if ($spot !== null) {
                    $spotNames[$spot->id] = $spot->name;
                }
            }
        }

        // 日付ごとにグループ化
        $grouped = [];
        foreach ($dtos as $dto) {
            $grouped[$dto->date][] = $dto->toArray();
        }

        ksort($grouped);

        return [
            'items' => $grouped,
            'spots' => $spotNames,
        ];
    }
}
