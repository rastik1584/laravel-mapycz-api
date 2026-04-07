<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Geocoding;

use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

final class SuggestParams extends BaseDTO
{
    public function __construct(
        public string $query,
        public ?string $lang = 'cs',
        public ?int $limit = null,
        public ?array $type = null,
        public ?array $locality = null,
        public ?array $preferBBox = null,
        public ?array $preferNear = null,
        public ?float $preferNearPrecision = null,
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if (trim($this->query) === '') {
            throw new InvalidMapyczParamsException('Query must not be empty.');
        }
        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }
        if ($this->limit !== null && $this->limit <= 0) {
            throw new InvalidMapyczParamsException('Limit must be a positive integer.');
        }
        if ($this->limit !== null && $this->limit > 15) {
            throw new InvalidMapyczParamsException('Limit must not exceed 15.');
        }
        if ($this->type !== null) {
            $allowedTypes = config('mapycz-api.allowed_params.geocodeEntityTypes', []);

            foreach ($this->type as $type) {
                if (! in_array($type, $allowedTypes, true)) {
                    throw new InvalidMapyczParamsException('Invalid geocode entity type: '.$type);
                }
            }
        }
        if ($this->locality !== null) {
            foreach ($this->locality as $locality) {
                if (! is_string($locality) || trim($locality) === '') {
                    throw new InvalidMapyczParamsException('Each locality filter must be a non-empty string.');
                }
            }
        }
        if ($this->preferBBox !== null) {
            if (count($this->preferBBox) !== 4) {
                throw new InvalidMapyczParamsException('preferBBox must contain exactly four numbers.');
            }

            foreach ($this->preferBBox as $item) {
                if (! is_numeric($item)) {
                    throw new InvalidMapyczParamsException('preferBBox must contain only numeric values.');
                }
            }
        }
        if ($this->preferNear !== null) {
            $this->normalizeCoordinates($this->preferNear);
        }
        if ($this->preferBBox !== null && $this->preferNear !== null) {
            throw new InvalidMapyczParamsException('preferBBox and preferNear cannot be used together.');
        }
        if ($this->preferNearPrecision !== null && $this->preferNearPrecision < 0) {
            throw new InvalidMapyczParamsException('preferNearPrecision must be zero or greater.');
        }
    }

    public function toArray(): array
    {
        $params = [
            'query' => $this->query,
        ];
        if (! is_null($this->lang)) {
            $params['lang'] = $this->lang;
        }
        if (! is_null($this->limit)) {
            $params['limit'] = $this->limit;
        }
        if (! is_null($this->type)) {
            $params['type'] = $this->type;
        }
        if (! is_null($this->locality)) {
            $params['locality'] = $this->locality;
        }
        if (! is_null($this->preferBBox)) {
            $params['preferBBox'] = [implode(',', array_map('strval', $this->preferBBox))];
        }
        if (! is_null($this->preferNear)) {
            $params['preferNear'] = [implode(',', $this->normalizeCoordinates($this->preferNear))];
        }
        if (! is_null($this->preferNearPrecision)) {
            $params['preferNearPrecision'] = $this->preferNearPrecision;
        }

        return $params;
    }
}
