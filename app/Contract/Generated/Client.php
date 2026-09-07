<?php

namespace App\Contract\Generated;

class Client extends \App\Contract\Generated\Runtime\Client\Client
{
    /**
     *
     * @return null|\App\Contract\Generated\Model\Booking[]
     */
    public function adminBookingsList()
    {
        return $this->executeEndpoint(new \App\Contract\Generated\Endpoint\AdminBookingsList());
    }
    /**
     * Создать Тип встречи.
     * @param \App\Contract\Generated\Model\MeetingTypeInput $requestBody
     *
     * @return null|\App\Contract\Generated\Model\MeetingType
     */
    public function adminMeetingTypesCreate(\App\Contract\Generated\Model\MeetingTypeInput $requestBody)
    {
        return $this->executeEndpoint(new \App\Contract\Generated\Endpoint\AdminMeetingTypesCreate($requestBody));
    }
    /**
     * Записаться на свободный слот.
     * @param \App\Contract\Generated\Model\BookingInput $requestBody
     *
     * @return null|\App\Contract\Generated\Model\Booking
     */
    public function bookingsCreate(\App\Contract\Generated\Model\BookingInput $requestBody)
    {
        return $this->executeEndpoint(new \App\Contract\Generated\Endpoint\BookingsCreate($requestBody));
    }
    /**
     *
     * @return null|\App\Contract\Generated\Model\MeetingType[]
     */
    public function meetingTypesList()
    {
        return $this->executeEndpoint(new \App\Contract\Generated\Endpoint\MeetingTypesList());
    }
    /**
     * Свободные слоты Типа встречи в окне записи.
     * @param int $id
     * @param array{
     *    "from": string,
     *    "to": string,
     * } $queryParameters
     *
     * @return null|\App\Contract\Generated\Model\SlotList|\App\Contract\Generated\Model\NotFoundError
     */
    public function meetingTypesSlots(int $id, array $queryParameters = [])
    {
        return $this->executeEndpoint(new \App\Contract\Generated\Endpoint\MeetingTypesSlots($id, $queryParameters));
    }
    /**
     * @param list<callable(\Symfony\Contracts\HttpClient\HttpClientInterface): \Symfony\Contracts\HttpClient\HttpClientInterface> $additionalPlugins HttpClientInterface decorator factories, applied left-to-right after the server URL decorator
     * @param list<\Symfony\Component\Serializer\Normalizer\NormalizerInterface|\Symfony\Component\Serializer\Normalizer\DenormalizerInterface> $additionalNormalizers
     */
    public static function create(?\Symfony\Contracts\HttpClient\HttpClientInterface $httpClient = null, array $additionalPlugins = [], array $additionalNormalizers = [])
    {
        if (null === $httpClient) {
            $httpClient = \Symfony\Component\HttpClient\HttpClient::create();
        }
        $plugins = [];
        if (count($additionalPlugins) > 0) {
            $plugins = array_merge($plugins, $additionalPlugins);
        }
        foreach ($plugins as $plugin) {
            $httpClient = $plugin($httpClient);
        }
        $normalizers = [new \Symfony\Component\Serializer\Normalizer\ArrayDenormalizer(), new \App\Contract\Generated\Normalizer\JaneObjectNormalizer()];
        if (count($additionalNormalizers) > 0) {
            $normalizers = array_merge($normalizers, $additionalNormalizers);
        }
        $serializer = new \Symfony\Component\Serializer\Serializer($normalizers, [new \Symfony\Component\Serializer\Encoder\JsonEncoder(new \Symfony\Component\Serializer\Encoder\JsonEncode(), new \Symfony\Component\Serializer\Encoder\JsonDecode(['json_decode_associative' => true])), new \App\Contract\Generated\Runtime\Client\FormEncoder()]);
        return new static($httpClient, $serializer);
    }
}