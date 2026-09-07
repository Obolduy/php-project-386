<?php

namespace App\Contract\Generated\Normalizer;

use Jane\Component\JsonSchemaRuntime\Reference;
use App\Contract\Generated\Runtime\Normalizer\CheckArray;
use App\Contract\Generated\Runtime\Normalizer\ValidatorTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
class SlotNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === \App\Contract\Generated\Model\Slot::class;
    }
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_object($data) && get_class($data) === \App\Contract\Generated\Model\Slot::class;
    }
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $object = new \App\Contract\Generated\Model\Slot();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (isset($data['$ref']) && !isset($data['type']) && !isset($data['properties']) && !isset($data['allOf'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        if (\array_key_exists('startsAt', $data)) {
            $date = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $data['startsAt']);
            if (false === $date) {
                throw new \App\Contract\Generated\Runtime\Normalizer\InvalidDateException($data['startsAt'], 'Y-m-d\TH:i:sP');
            }
            $object->startsAt = $date;
            unset($data['startsAt']);
        }
        if (\array_key_exists('endsAt', $data)) {
            $date_1 = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $data['endsAt']);
            if (false === $date_1) {
                throw new \App\Contract\Generated\Runtime\Normalizer\InvalidDateException($data['endsAt'], 'Y-m-d\TH:i:sP');
            }
            $object->endsAt = $date_1;
            unset($data['endsAt']);
        }
        foreach ($data as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $object[$key] = $value;
            }
        }
        return $object;
    }
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $dataArray = [];
        $dataArray['startsAt'] = ($data->startsAt ?? null)->format('Y-m-d\TH:i:sP');
        $dataArray['endsAt'] = ($data->endsAt ?? null)->format('Y-m-d\TH:i:sP');
        foreach ($data->additionalPropertyEntries() as $key => $value) {
            if (preg_match('/.*/', (string) $key)) {
                $dataArray[$key] = $value;
            }
        }
        return $dataArray;
    }
    public function getSupportedTypes(?string $format = null): array
    {
        return [\App\Contract\Generated\Model\Slot::class => false];
    }
}