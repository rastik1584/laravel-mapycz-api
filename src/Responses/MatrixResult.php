<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class MatrixResult
{
    /**
     * @param  array<int, array<int, MatrixCell>>  $matrix
     */
    public function __construct(
        public readonly array $matrix,
    ) {}

    public static function fromArray(array $payload): self
    {
        $matrix = array_map(
            static fn (array $row): array => array_map(
                static fn (array $cell): MatrixCell => MatrixCell::fromArray($cell),
                $row
            ),
            $payload['matrix'] ?? []
        );

        return new self($matrix);
    }
}

final class MatrixCell
{
    public function __construct(
        public readonly int $length,
        public readonly int $duration,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            length: (int) ($payload['length'] ?? 0),
            duration: (int) ($payload['duration'] ?? 0),
        );
    }
}
