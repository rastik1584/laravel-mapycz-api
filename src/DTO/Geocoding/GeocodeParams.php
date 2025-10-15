<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Geocoding;

use InvalidArgumentException;
use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;

final class GeocodeParams extends BaseDTO
{
    public function __construct(
        public string $query,
        public ?string $lang = 'cs',
        public ?int $limit = null,
    ) {
        $this->validate();
    }

    protected function validate(): void
    {
        if (trim($this->query) === '') {
            throw new InvalidArgumentException('Query must not be empty.');
        }
        if ($this->lang !== null) {
            $this->validateLang($this->lang);
        }
        if ($this->limit !== null && $this->limit <= 0) {
            throw new InvalidArgumentException('Limit must be a positive integer.');
        }
    }

    public function toArray(): array
    {
        $params = [
            'query' => $this->query,
        ];
        if (!is_null($this->lang)) {
            $params['lang'] = $this->lang;
        }
        if (!is_null($this->limit)) {
            $params['limit'] = $this->limit;
        }
        return $params;
    }
}
