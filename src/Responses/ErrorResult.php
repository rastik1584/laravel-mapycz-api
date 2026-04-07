<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class ErrorResult
{
    /**
     * @param  array<int, ErrorDetail>  $detail
     */
    public function __construct(
        public readonly array $detail,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            detail: array_map(
                static fn (mixed $item): ErrorDetail => ErrorDetail::fromMixed($item),
                $payload['detail'] ?? []
            ),
        );
    }

    public function firstDetail(): ?ErrorDetail
    {
        return $this->detail[0] ?? null;
    }

    public function toArray(): array
    {
        return [
            'detail' => array_map(static fn (ErrorDetail $item): array => $item->toArray(), $this->detail),
        ];
    }
}

final class ErrorDetail
{
    /**
     * @param  array<int, string|int>  $loc
     */
    public function __construct(
        public readonly ?string $msg = null,
        public readonly ?int $errorCode = null,
        public readonly ?string $type = null,
        public readonly array $loc = [],
        public readonly mixed $raw = null,
    ) {}

    public static function fromMixed(mixed $payload): self
    {
        if (! is_array($payload)) {
            return new self(raw: $payload);
        }

        return new self(
            msg: isset($payload['msg']) ? (string) $payload['msg'] : null,
            errorCode: isset($payload['errorCode']) ? (int) $payload['errorCode'] : null,
            type: isset($payload['type']) ? (string) $payload['type'] : null,
            loc: isset($payload['loc']) && is_array($payload['loc']) ? $payload['loc'] : [],
            raw: $payload,
        );
    }

    public function toArray(): array
    {
        return [
            'msg' => $this->msg,
            'errorCode' => $this->errorCode,
            'type' => $this->type,
            'loc' => $this->loc,
            'raw' => $this->raw,
        ];
    }
}
