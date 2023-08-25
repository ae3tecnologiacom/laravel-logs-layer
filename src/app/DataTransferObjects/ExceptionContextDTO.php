<?php

namespace Ae3\LaravelLogsLayer\app\DataTransferObjects;


use Spatie\DataTransferObject\DataTransferObject;

class ExceptionContextDTO extends DataTransferObject
{
    /**
     * @var string|null
     */
    public ?string $code;

    /**
     * Apenas o primeiro nome do namespace do seu projeto
     * @var string|null
     */
    public ?string $root_namespace;

    /**
     * @var array|null
     */
    public ?array $tags;

    /**
     * @var object|null
     */
    public ?object $current_user;

    /**
     * @var string|null
     */
    public ?string $current_url;

    /**
     * @var array|null
     */
    public ?array $custom_data;

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self([
            'code' => $data['code'] ?? null,
            'root_namespace' => $data['root_namespace'] ?? null,
            'tags' => $data['tags'] ?? null,
            'current_user' => $data['current_user'] ?? null,
            'current_url' => $data['current_url'] ?? null,
            'custom_data' => $data['custom_data'] ?? null,
        ]);
    }
}