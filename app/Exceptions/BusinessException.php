<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class BusinessException extends \RuntimeException implements HttpExceptionInterface
{
    private $statusCode;

    private $headers;

    public function __construct(int $statusCode, string $message = null, \Throwable $previous = null, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
