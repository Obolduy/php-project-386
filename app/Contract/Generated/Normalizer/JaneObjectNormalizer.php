<?php

namespace App\Contract\Generated\Normalizer;

use App\Contract\Generated\Runtime\Normalizer\CheckArray;
use App\Contract\Generated\Runtime\Normalizer\ValidatorTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class JaneObjectNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    protected $normalizers = [
        
        \App\Contract\Generated\Model\Booking::class => \App\Contract\Generated\Normalizer\BookingNormalizer::class,
        
        \App\Contract\Generated\Model\BookingInput::class => \App\Contract\Generated\Normalizer\BookingInputNormalizer::class,
        
        \App\Contract\Generated\Model\MeetingType::class => \App\Contract\Generated\Normalizer\MeetingTypeNormalizer::class,
        
        \App\Contract\Generated\Model\MeetingTypeInput::class => \App\Contract\Generated\Normalizer\MeetingTypeInputNormalizer::class,
        
        \App\Contract\Generated\Model\NotFoundError::class => \App\Contract\Generated\Normalizer\NotFoundErrorNormalizer::class,
        
        \App\Contract\Generated\Model\Slot::class => \App\Contract\Generated\Normalizer\SlotNormalizer::class,
        
        \App\Contract\Generated\Model\SlotList::class => \App\Contract\Generated\Normalizer\SlotListNormalizer::class,
        
        \App\Contract\Generated\Model\SlotTakenError::class => \App\Contract\Generated\Normalizer\SlotTakenErrorNormalizer::class,
        
        \Jane\Component\JsonSchemaRuntime\Reference::class => \App\Contract\Generated\Runtime\Normalizer\ReferenceNormalizer::class,
    ], $normalizersCache = [];
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return array_key_exists($type, $this->normalizers);
    }
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_object($data) && array_key_exists(get_class($data), $this->normalizers);
    }
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $normalizerClass = $this->normalizers[get_class($data)];
        $normalizer = $this->getNormalizer($normalizerClass);
        return $normalizer->normalize($data, $format, $context);
    }
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $denormalizerClass = $this->normalizers[$type];
        $denormalizer = $this->getNormalizer($denormalizerClass);
        return $denormalizer->denormalize($data, $type, $format, $context);
    }
    private function getNormalizer(string $normalizerClass)
    {
        return $this->normalizersCache[$normalizerClass] ?? $this->initNormalizer($normalizerClass);
    }
    private function initNormalizer(string $normalizerClass)
    {
        $normalizer = new $normalizerClass();
        $normalizer->setNormalizer($this->normalizer);
        $normalizer->setDenormalizer($this->denormalizer);
        $this->normalizersCache[$normalizerClass] = $normalizer;
        return $normalizer;
    }
    public function getSupportedTypes(?string $format = null): array
    {
        return array_combine(array_keys($this->normalizers), array_fill(0, count($this->normalizers), false));
    }
}