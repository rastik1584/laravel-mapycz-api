<?php

declare(strict_types=1);

namespace Rastik1584\LaravelMapyczApi\Exceptions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Rastik1584\LaravelMapyczApi\Responses\ErrorResult;
use Throwable;

final class MapyczApiRequestException extends MapyczApiException
{
    public static function fromRequestException(RequestException $exception): self
    {
        $response = $exception->response;
        $payload = self::decodeErrorPayload($response);

        return new self(
            message: self::buildMessage($exception, $payload),
            code: (int) $exception->getCode(),
            previous: $exception,
            statusCode: $response->status(),
            headers: $response->headers(),
            body: $response->body(),
            error: $payload,
        );
    }

    /**
     * @param  array<string, array<int, string>>  $headers
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        public readonly ?int $statusCode = null,
        public readonly array $headers = [],
        public readonly ?string $body = null,
        public readonly ?ErrorResult $error = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function firstErrorCode(): ?int
    {
        return $this->error?->firstDetail()?->errorCode;
    }

    public function firstErrorMessage(): ?string
    {
        return $this->error?->firstDetail()?->msg;
    }

    private static function decodeErrorPayload(Response $response): ?ErrorResult
    {
        $decoded = $response->json();

        if (! is_array($decoded) || ! array_key_exists('detail', $decoded)) {
            return null;
        }

        return ErrorResult::fromArray($decoded);
    }

    private static function buildMessage(RequestException $exception, ?ErrorResult $payload): string
    {
        $detail = $payload?->firstDetail();

        if ($detail?->msg !== null) {
            return $detail->msg;
        }

        return $exception->getMessage();
    }
}
