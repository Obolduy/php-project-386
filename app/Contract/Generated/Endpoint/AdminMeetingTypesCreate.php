<?php

namespace App\Contract\Generated\Endpoint;

class AdminMeetingTypesCreate extends \App\Contract\Generated\Runtime\Client\BaseEndpoint implements \App\Contract\Generated\Runtime\Client\Endpoint
{
    /**
     * Создать Тип встречи.
     * @param \App\Contract\Generated\Model\MeetingTypeInput $requestBody
     */
    public function __construct(\App\Contract\Generated\Model\MeetingTypeInput $requestBody)
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
        return '/api/admin/meeting-types';
    }
    public function getBody(\Symfony\Component\Serializer\SerializerInterface $serializer): array
    {
        if ($this->body instanceof \App\Contract\Generated\Model\MeetingTypeInput) {
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
     *
     * @return null|\App\Contract\Generated\Model\MeetingType
     */
    protected function transformResponseBody(\Symfony\Contracts\HttpClient\ResponseInterface $response, \Symfony\Component\Serializer\SerializerInterface $serializer, ?string $contentType = null)
    {
        $status = $response->getStatusCode();
        $body = $response->getContent(false);
        if ($contentType !== null && (201 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            return $serializer->deserialize($body, 'App\Contract\Generated\Model\MeetingType', 'json');
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