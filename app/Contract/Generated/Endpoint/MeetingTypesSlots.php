<?php

namespace App\Contract\Generated\Endpoint;

class MeetingTypesSlots extends \App\Contract\Generated\Runtime\Client\BaseEndpoint implements \App\Contract\Generated\Runtime\Client\Endpoint
{
    protected $id;
    /**
     * Свободные слоты Типа встречи в окне записи.
     * @param int $id
     * @param array{
     *    "from"?: string,
     *    "to"?: string,
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
        $optionsResolver->setRequired([]);
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
     * @throws \App\Contract\Generated\Exception\MeetingTypesSlotsNotFoundException
     *
     * @return null|\App\Contract\Generated\Model\SlotList
     */
    protected function transformResponseBody(\Symfony\Contracts\HttpClient\ResponseInterface $response, \Symfony\Component\Serializer\SerializerInterface $serializer, ?string $contentType = null)
    {
        $status = $response->getStatusCode();
        $body = $response->getContent(false);
        if ($contentType !== null && (200 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            return $serializer->deserialize($body, 'App\Contract\Generated\Model\SlotList', 'json');
        }
        if ($contentType !== null && (404 === $status && stripos(strtolower($contentType), 'application/json') !== false)) {
            throw new \App\Contract\Generated\Exception\MeetingTypesSlotsNotFoundException($serializer->deserialize($body, 'App\Contract\Generated\Model\NotFoundError', 'json'), $response);
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