<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\DTO\Routing;

use Exception;
use Rastik1584\LaravelMapyczApi\DTO\BaseDTO;
use Rastik1584\LaravelMapyczApi\Exceptions\InvalidMapyczParamsException;

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
        public string $format = 'geojson',
        public array $waypoints = [],
        public bool $avoidToll = false,
        public bool $avoidHighways = false,
        public ?string $departure = null,
    ) {
        $this->validate();
    }

    /**
     * @throws Exception
     */
    protected function validate(): void
    {
        $routeTypes = $this->supportedRouteTypes();
        $routingFormats = config('mapycz-api.allowed_params.routingFormats', ['geojson', 'polyline', 'polyline6']);

        $this->validateLang($this->lang);

        $this->normalizeCoordinates($this->start);
        $this->normalizeCoordinates($this->end);

        if (! in_array($this->routeType, $routeTypes)) {
            throw new InvalidMapyczParamsException('Invalid route type. Allowed route types: '.implode(', ', $routeTypes).'.');
        }

        if (! in_array($this->format, $routingFormats)) {
            throw new InvalidMapyczParamsException('Invalid format. Allowed formats: '.implode(', ', $routingFormats).'.');
        }

        if (! is_array($this->waypoints)) {
            throw new InvalidMapyczParamsException('Waypoints must be an array');
        }

        foreach ($this->waypoints as $waypoint) {
            $this->normalizeCoordinates($waypoint);
        }
        if (count($this->waypoints) > 15) {
            throw new InvalidMapyczParamsException('Waypoints must not exceed 15 items.');
        }

        if ($this->departure !== null && strtotime($this->departure) === false) {
            throw new InvalidMapyczParamsException('Departure must be a valid ISO-8601 date-time string.');
        }
    }

    public function toArray(): array
    {
        $params = [
            'start' => implode(',', $this->normalizeCoordinates($this->start)),
            'end' => implode(',', $this->normalizeCoordinates($this->end)),
            'routeType' => $this->routeType,
            'lang' => $this->lang,
            'format' => $this->format,
            'avoidToll' => $this->avoidToll,
            'avoidHighways' => $this->avoidHighways,
        ];

        if (! empty($this->waypoints)) {
            $params['waypoints'] = collect($this->waypoints)
                ->map(fn ($waypoint) => implode(',', $this->normalizeCoordinates($waypoint)))
                ->implode(';');
        }

        if ($this->departure !== null) {
            $params['departure'] = $this->departure;
        }

        if ($this->lang === 'cs') {
            unset($params['lang']);
        }

        return $params;
    }
}
