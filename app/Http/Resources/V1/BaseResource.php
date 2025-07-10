<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseResource extends JsonResource
{
    public  $resource;

    abstract public function prePareData($request): array;

    public static function collection($resource)
    {
        return new class($resource, static::class) extends ResourceCollection {
            protected string $resourceClass;

            public function __construct($resource, string $resourceClass)
            {
                parent::__construct($resource);
                $this->resourceClass = $resourceClass;
            }

            public function toArray($request): array
            {
                $resourceClass = $this->resourceClass;

                $transformed = $this->collection->map(function ($item) use ($request, $resourceClass) {
                    /** @var BaseResource $resourceInstance */
                    $resourceInstance = new $resourceClass($item);
                    return $resourceInstance->prePareData($request);
                });

                return [
                    'code' => 200,
                    'success' => true,
                    'message' => __('public.operation_successful'),
                    'result' => $transformed,
                ];
            }
        };
    }

    final public function toArray($request): array
    {
        return [
            'code' => 200,
            'success' => true,
            'message' => __('public.operation_successful'),
            'result' => $this->prePareData($request)
        ];
    }
}