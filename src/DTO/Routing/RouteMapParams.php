<?php
declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Routing;

use Exception;
use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;

/**
 * https://api.mapy.com/v1/docs/routing/#/routing/basic_route_v1_routing_route_get
 */
final class RouteMapParams extends BaseDTO
{

    /**
     * @throws Exception
     */
    public function __construct(
        public array $start,
        public array $end,
        public string $routeType,
        public string $lang = 'cs',
        public string $format,
        public array $waypoints,
        public bool $avoidToll = false,
    ) {
        $this->validate();
    }

    /**
     * @throws Exception
     */
    protected function validate(): void
    {
        $routeTypes = config('mapycz-api.allowed_params.routeTypes', ['fastest', 'shortest', 'pedestrian', 'bicycle', 'car']);
        $routingFormats = config('mapycz-api.allowed_params.routingFormats', ['geojson', 'polyline', 'polyline6']);

        $this->validateLang($this->lang);

        if (!is_array($this->start) || count($this->start) !== 2) {
            throw new \InvalidArgumentException('Start coordinates must be an array with exactly 2 elements [longitude,latitude]');
        }

        if (!is_array($this->end) || count($this->end) !== 2) {
            throw new \InvalidArgumentException('End coordinates must be an array with exactly 2 elements [longitude,latitude]');
        }

        if (!in_array($this->routeType, $routeTypes)) {
            throw new \InvalidArgumentException('Invalid route type. Allowed route types: ' . implode(', ', $routeTypes) . '.');
        }

        if (!in_array($this->format, $routingFormats)) {
            throw new \InvalidArgumentException('Invalid format. Allowed formats: ' . implode(', ', $routingFormats) . '.');
        }

        if (!is_array($this->waypoints)) {
            throw new \InvalidArgumentException('Waypoints must be an array');
        }

        foreach ($this->waypoints as $waypoint) {
            if (!is_array($waypoint) || count($waypoint) !== 2) {
                throw new \InvalidArgumentException('Each waypoint must be an array with exactly 2 elements');
            }
        }
    }

    public function toArray(): array
    {
        $params = [
            'start' => implode(',', normalizeCoordinates($this->start)),
            'end' => implode(',', normalizeCoordinates($this->end)),
            'routeType' => $this->routeType,
            'lang' => $this->lang,
            'format' => $this->format,
            'avoidToll' => $this->avoidToll,
            'waypoints' => collect($this->waypoints)->map(fn($waypoint) => implode(',', normalizeCoordinates($waypoint)))->implode(';'),
        ];
        if ($this->lang === 'cs') unset($params['lang']);

        return $params;
    }
}