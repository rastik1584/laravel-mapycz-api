<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Routing;

use Exception;
use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;

/**
 * https://api.mapy.com/v1/docs/routing/#/routing/matrix_m_v1_routing_matrix_m_get
 */
final class MatrixMapParams extends BaseDTO
{
    /**
     * @throws Exception
     */
    public function __construct(
        public array $starts,
        public string $routeType,
        public array $ends = [],
        public bool $avoidToll = false,
        public string $lang = 'cs',
    ) {
        $this->validate();
    }

    /**
     * @throws Exception
     */
    protected function validate(): void
    {
        $routeTypes = config('mapycz-api.allowed_params.routeTypes', ['fastest', 'shortest', 'pedestrian', 'bicycle', 'car']);

        $this->validateLang($this->lang);

        if (!$this->validateCoordinatesArray($this->starts)) {
            throw new \InvalidArgumentException('Start coordinates must be an array with exactly 2 elements [longitude,latitude]');
        }

        if (!empty($this->ends) && !$this->validateCoordinatesArray($this->ends)) {
            throw new \InvalidArgumentException('End coordinates must be an array with exactly 2 elements [longitude,latitude]');
        }

        if (!in_array($this->routeType, $routeTypes)) {
            throw new \InvalidArgumentException('Invalid route type. Allowed route types: ' . implode(', ', $routeTypes) . '.');
        }
    }

    public function toArray(): array
    {
        $params = [
            'starts' => implode(',', normalizeCoordinates($this->starts)),
            'end' => implode(',', normalizeCoordinates($this->ends)),
            'routeType' => $this->routeType,
            'avoidToll' => $this->avoidToll,
            'lang' => $this->lang,
        ];
        if ($this->lang === 'cs') unset($params['lang']);

        return $params;
    }
}