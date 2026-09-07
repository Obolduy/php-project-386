<?php

namespace App\Contract\Generated\Endpoint;

class BookingsCreate extends \App\Contract\Generated\Runtime\Client\BaseEndpoint implements \App\Contract\Generated\Runtime\Client\Endpoint
{
    /**
     * Записаться на свободный слот.
     * @param \App\Contract\Generated\Model\BookingInput $requestBody
     */
    public function __construct(\App\Contract\Generated\Model\BookingInput $requestBody)
    {
        $this->body = $requestBody;
    }
    use \App\Contract\Generated\Runtime\Client\EndpointTrait;
    public function getMethod(): string
    {
        return 'POST';
    }
    public function getUri(): string
    {
        return '/api/bookings';
    }
    public function getBody(\Symfony\Component\Serializer\SerializerInterface $serializer): array
    {
        if ($this->body instanceof \App\Contract\Generated\Model\BookingInput) {
            return [['Content-Type' => ['application/json']], \App\Contract\Generated\Runtime\Client\JsonPayload::encode($serializer, $this->body)];
        }
        return [[], null];
    }
    public function getExtraHeaders(): array
    {
        return ['Accept' => ['application/json']];
    }
    /**
     * {@inheritdoc}
     *
     * @throws \App\Contract\Generated\Exception\BookingsCreateNotFoundException
     * @throws \App\Contract\Generated\Exception\BookingsCreateConflictException
     * @throws \App\Contract\Generated\Exception\BookingsCreateUnprocessableEntityException
     *
     * @return null|\App\Contract\Generated\Model\Booking
     */
    protected function transformResponseBody(\Symfony\Contracts\HttpClient\ResponseInterface $response, \Symfony\Component\Serializer\SerializerInterface $serializer, ?string $contentType = null)
    {
        $status = $response->getStatusCode();
        $body = $response->getContent(false);
        if ($contentType !== null && (201 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            return $serializer->deserialize($body, 'App\Contract\Generated\Model\Booking', 'json');
        }
        if ($contentType !== null && (404 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            throw new \App\Contract\Generated\Exception\BookingsCreateNotFoundException($serializer->deserialize($body, 'App\Contract\Generated\Model\NotFoundError', 'json'), $response);
        }
        if ($contentType !== null && (409 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            throw new \App\Contract\Generated\Exception\BookingsCreateConflictException($serializer->deserialize($body, 'App\Contract\Generated\Model\SlotTakenError', 'json'), $response);
        }
        if ($contentType !== null && (422 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            throw new \App\Contract\Generated\Exception\BookingsCreateUnprocessableEntityException($serializer->deserialize($body, 'App\Contract\Generated\Model\ValidationError', 'json'), $response);
        }
    }
    public function getAuthenticationScopes(): array
    {
        return [];
    }
    public function getFetchMode(): string
    {
        return \Jane\Component\OpenApiRuntime\Client\FetchMode::Eager->value;
    }
}