<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\SpotMemo as SpotMemoModel;
use Packages\Domain\Entities\SpotMemo;
use Packages\Domain\Repositories\SpotMemoRepositoryInterface;

class EloquentSpotMemoRepository implements SpotMemoRepositoryInterface
{
    /**
     * @return SpotMemo[]
     */
    public function findBySpotId(int $spotId): array
    {
        return SpotMemoModel::query()
            ->where('spot_id', $spotId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn (SpotMemoModel $model): SpotMemo => $this->toEntity($model))
            ->all();
    }

    public function save(SpotMemo $memo): SpotMemo
    {
        $model = $memo->id === 0
            ? new SpotMemoModel()
            : SpotMemoModel::findOrFail($memo->id);

        $model->fill([
            'spot_id' => $memo->spotId,
            'user_id' => $memo->userId,
            'body' => $memo->body,
        ]);
        $model->save();

        return $this->toEntity($model);
    }

    private function toEntity(SpotMemoModel $model): SpotMemo
    {
        return new SpotMemo(
            id: $model->id,
            spotId: $model->spot_id,
            userId: $model->user_id,
            body: $model->body,
        );
    }
}
