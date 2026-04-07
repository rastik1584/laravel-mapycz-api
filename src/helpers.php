<?php

if (! function_exists('normalizeCoordinates')) {
    /**
     * Normalize coordinate input and return as ['lon' => ..., 'lat' => ...]
     */
    function normalizeCoordinates(array $coords): array
    {
        // Named format: ['lat' => ..., 'lon' => ...] or ['lat' => ..., 'lng' => ...]
        if (isset($coords['lat']) && (isset($coords['lon']) || isset($coords['lng']))) {
            return [
                'lon' => $coords['lng'] ?? $coords['lon'],
                'lat' => $coords['lat'],
            ];
        }

        // Numeric format: [lat, lon] or [lon, lat]
        if (isset($coords[0], $coords[1])) {
            $first = floatval($coords[0]);
            $second = floatval($coords[1]);

            // lat: −90 to +90, lon: −180 to +180
            if (abs($first) <= 90 && abs($second) <= 180) {
                return ['lat' => $first, 'lon' => $second];
            }

            return ['lon' => $first, 'lat' => $second];
        }

        throw new InvalidArgumentException('Invalid coordinate input: '.json_encode($coords));
    }
}

if (! function_exists('normalizeCoordinatesBatch')) {
    /**
     * Normalize array of coordinates
     */
    function normalizeCoordinatesBatch(array $points): array
    {
        return array_map(fn ($point) => normalizeCoordinates($point), $points);
    }
}
