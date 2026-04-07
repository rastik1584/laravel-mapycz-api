<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Responses;

final class TimezoneInfoResult
{
    public function __construct(
        public readonly TimezoneInfo $timezone,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            timezone: TimezoneInfo::fromArray($payload['timezone'] ?? [])
        );
    }
}

final class TimezoneInfo
{
    public function __construct(
        public readonly string $timezoneName,
        public readonly string $currentTimeAbbreviation,
        public readonly string $standardTimeAbbreviation,
        public readonly string $currentLocalTime,
        public readonly string $currentUtcTime,
        public readonly int $currentUtcOffsetSeconds,
        public readonly int $standardUtcOffsetSeconds,
        public readonly bool $hasDst,
        public readonly bool $isDstActive,
        public readonly ?DstInfo $dstInfo = null,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            timezoneName: (string) ($payload['timezoneName'] ?? ''),
            currentTimeAbbreviation: (string) ($payload['currentTimeAbbreviation'] ?? ''),
            standardTimeAbbreviation: (string) ($payload['standardTimeAbbreviation'] ?? ''),
            currentLocalTime: (string) ($payload['currentLocalTime'] ?? ''),
            currentUtcTime: (string) ($payload['currentUtcTime'] ?? ''),
            currentUtcOffsetSeconds: (int) ($payload['currentUtcOffsetSeconds'] ?? 0),
            standardUtcOffsetSeconds: (int) ($payload['standardUtcOffsetSeconds'] ?? 0),
            hasDst: (bool) ($payload['hasDst'] ?? false),
            isDstActive: (bool) ($payload['isDstActive'] ?? false),
            dstInfo: isset($payload['dstInfo']) && is_array($payload['dstInfo'])
                ? DstInfo::fromArray($payload['dstInfo'])
                : null,
        );
    }
}

final class DstInfo
{
    public function __construct(
        public readonly string $dstAbbreviation,
        public readonly string $dstStartUtcTime,
        public readonly string $dstStartLocalTime,
        public readonly string $dstEndUtcTime,
        public readonly string $dstEndLocalTime,
        public readonly int $dstOffsetSeconds,
        public readonly int $dstDurationSeconds,
    ) {}

    public static function fromArray(array $payload): self
    {
        return new self(
            dstAbbreviation: (string) ($payload['dstAbbreviation'] ?? ''),
            dstStartUtcTime: (string) ($payload['dstStartUtcTime'] ?? ''),
            dstStartLocalTime: (string) ($payload['dstStartLocalTime'] ?? ''),
            dstEndUtcTime: (string) ($payload['dstEndUtcTime'] ?? ''),
            dstEndLocalTime: (string) ($payload['dstEndLocalTime'] ?? ''),
            dstOffsetSeconds: (int) ($payload['dstOffsetSeconds'] ?? 0),
            dstDurationSeconds: (int) ($payload['dstDurationSeconds'] ?? 0),
        );
    }
}
