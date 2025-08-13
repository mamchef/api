<?php

namespace App\Services;

use App\DTOs\Admin\Tag\TagStoreDTO;
use App\DTOs\Admin\Tag\TagUpdateDTO;
use App\Models\Tag;
use App\Services\Interfaces\TagServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TagService implements TagServiceInterface
{
    /** @inheritDoc */
    public function all(
        array $filters,
        array $relations = [],
        ?int $pagination = null
    ): Collection|LengthAwarePaginator|array {
        $tags = Tag::query()->filter($filters)->with($relations);
        return $pagination ? $tags->paginate($pagination) : $tags->get();
    }

    /** @inheritDoc */
    public function show(int $tagId, array $relations = []): Tag
    {
        return Tag::query()->with($relations)->findOrFail($tagId);
    }

    /** @inheritDoc */
    public function store(TagStoreDTO $dto): Tag
    {
        return Tag::query()->create($dto->toArray());
    }

    /** @inheritDoc */
    public function update(int $tagId, TagUpdateDTO $dto): Tag
    {
        $tag = $this->show($tagId);
        $tag->update($dto->toArray());
        return $tag;
    }

    /** @inheritDoc */
    public function destroy(int $tagId): void
    {
        $tag = $this->show($tagId);
        $parentedTags = Tag::query()->where('tag_id', $tagId)->get();

        /** @var Tag $parentedTag */
        foreach ($parentedTags as $parentedTag) {
            $parentedTag->tag_id = null;
            $parentedTag->save();
        }
        $tag->delete();
    }
}