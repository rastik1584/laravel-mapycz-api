<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Routing;

use Exception;
use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

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
        $routeTypes = $this->supportedRouteTypes();

        $this->validateLang($this->lang);

        if (! $this->validateCoordinatesArray($this->starts)) {
            throw new InvalidMapyczParamsException('Start coordinates must be an array with exactly 2 elements [longitude,latitude]');
        }
        if (count($this->starts) < 1 || count($this->starts) > 100) {
            throw new InvalidMapyczParamsException('Starts must contain between 1 and 100 points.');
        }

        if (! empty($this->ends) && ! $this->validateCoordinatesArray($this->ends)) {
            throw new InvalidMapyczParamsException('End coordinates must be an array with exactly 2 elements [longitude,latitude]');
        }
        if (count($this->ends) > 100) {
            throw new InvalidMapyczParamsException('Ends must not exceed 100 points.');
        }
        if (empty($this->ends) && count($this->starts) < 2) {
            throw new InvalidMapyczParamsException('At least two start points are required for NxN matrix routing.');
        }
        $matrixSize = empty($this->ends) ? count($this->starts) * count($this->starts) : count($this->starts) * count($this->ends);
        if ($matrixSize > 100) {
            throw new InvalidMapyczParamsException('Matrix size must not exceed 100 route combinations.');
        }

        if (! in_array($this->routeType, $routeTypes)) {
            throw new InvalidMapyczParamsException('Invalid route type. Allowed route types: '.implode(', ', $routeTypes).'.');
        }
    }

    public function toArray(): array
    {
        $params = [
            'starts' => collect($this->normalizeCoordinatesBatch($this->starts))
                ->map(fn (array $point) => implode(',', $point))
                ->implode(';'),
            'routeType' => $this->routeType,
            'avoidToll' => $this->avoidToll,
            'lang' => $this->lang,
        ];

        if (! empty($this->ends)) {
            $params['ends'] = collect($this->normalizeCoordinatesBatch($this->ends))
                ->map(fn (array $point) => implode(',', $point))
                ->implode(';');
        }

        if ($this->lang === 'cs') {
            unset($params['lang']);
        }

        return $params;
    }
}
