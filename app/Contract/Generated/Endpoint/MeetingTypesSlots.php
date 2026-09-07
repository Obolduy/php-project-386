<?php

namespace App\Contract\Generated\Endpoint;

class MeetingTypesSlots extends \App\Contract\Generated\Runtime\Client\BaseEndpoint implements \App\Contract\Generated\Runtime\Client\Endpoint
{
    protected $id;
    /**
     * Свободные слоты Типа встречи в окне записи.
     * @param int $id
     * @param array{
     *    "from": string,
     *    "to": string,
     * } $queryParameters
     */
    public function __construct(int $id, array $queryParameters = [])
    {
        $this->id = $id;
        $this->queryParameters = $queryParameters;
    }
    use \App\Contract\Generated\Runtime\Client\EndpointTrait;
    public function getMethod(): string
    {
        return 'GET';
    }
    public function getUri(): string
    {
        return str_replace(['{id}'], [rawurlencode($this->id)], '/api/meeting-types/{id}/slots');
    }
    public function getBody(\Symfony\Component\Serializer\SerializerInterface $serializer): array
    {
        return [[], null];
    }
    public function getExtraHeaders(): array
    {
        return ['Accept' => ['application/json']];
    }
    protected function getQueryOptionsResolver(): \Symfony\Component\OptionsResolver\OptionsResolver
    {
        $optionsResolver = parent::getQueryOptionsResolver();
        $optionsResolver->setDefined(['from', 'to']);
        $optionsResolver->setRequired(['from', 'to']);
        $optionsResolver->setDefaults([]);
        $optionsResolver->addAllowedTypes('from', ['string']);
        $optionsResolver->addAllowedTypes('to', ['string']);
        return $optionsResolver;
    }
    protected function getQueryStyles(): array
    {
        return ['from' => ['style' => 'form', 'explode' => false], 'to' => ['style' => 'form', 'explode' => false]];
    }
    /**
     * {@inheritdoc}
     *
     *
     * @return null|\App\Contract\Generated\Model\SlotList|\App\Contract\Generated\Model\NotFoundError
     */
    protected function transformResponseBody(\Symfony\Contracts\HttpClient\ResponseInterface $response, \Symfony\Component\Serializer\SerializerInterface $serializer, ?string $contentType = null)
    {
        $status = $response->getStatusCode();
        $body = $response->getContent(false);
        if ($contentType !== null && (200 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            return $serializer->deserialize($body, 'App\Contract\Generated\Model\SlotList', 'json');
        }
        if (stripos(strtolower($contentType), 'application/json') !== false) {
            return $serializer->deserialize($body, 'App\Contract\Generated\Model\NotFoundError', 'json');
        }
    }
    public function getAuthenticationScopes(): array
    {
        return [];
    }
    public function getFetchMode(): string
    {
        return \Jane\Component\OpenApiRuntime\Client\FetchMode::Lazy->value;
    }
}