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
class ValidationErrorNormalizer implements DenormalizerInterface, NormalizerInterface, DenormalizerAwareInterface, NormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    use NormalizerAwareTrait;
    use CheckArray;
    use ValidatorTrait;
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === \App\Contract\Generated\Model\ValidationError::class;
    }
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_object($data) && get_class($data) === \App\Contract\Generated\Model\ValidationError::class;
    }
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $object = new \App\Contract\Generated\Model\ValidationError();
        if (null === $data || false === \is_array($data)) {
            return $object;
        }
        if (isset($data['$ref']) && !isset($data['type']) && !isset($data['properties']) && !isset($data['allOf'])) {
            return new Reference($data['$ref'], $context['document-origin']);
        }
        if (isset($data['$recursiveRef'])) {
            return new Reference($data['$recursiveRef'], $context['document-origin']);
        }
        if (\array_key_exists('code', $data)) {
            $object->code = $data['code'];
            unset($data['code']);
        }
        if (\array_key_exists('message', $data)) {
            $object->message = $data['message'];
            unset($data['message']);
        }
        if (\array_key_exists('fields', $data)) {
            $values = new \App\Contract\Generated\Runtime\JsonObject();
            foreach ($data['fields'] as $key => $value) {
                $values_1 = [];
                foreach ($value as $value_1) {
                    $values_1[] = $value_1;
                }
                $values[$key] = $values_1;
            }
            $object->fields = $values;
            unset($data['fields']);
        }
        foreach ($data as $key_1 => $value_2) {
            if (preg_match('/.*/', (string) $key_1)) {
                $object[$key_1] = $value_2;
            }
        }
        return $object;
    }
    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $dataArray = [];
        $dataArray['code'] = $data->code ?? null;
        $dataArray['message'] = $data->message ?? null;
        $values = new \App\Contract\Generated\Runtime\JsonObject();
        foreach ($data->fields ?? null as $key => $value) {
            $values_1 = [];
            foreach ($value as $value_1) {
                $values_1[] = $value_1;
            }
            $values[$key] = $values_1;
        }
        $dataArray['fields'] = $values;
        foreach ($data->additionalPropertyEntries() as $key_1 => $value_2) {
            if (preg_match('/.*/', (string) $key_1)) {
                $dataArray[$key_1] = $value_2;
            }
        }
        return $dataArray;
    }
    public function getSupportedTypes(?string $format = null): array
    {
        return [\App\Contract\Generated\Model\ValidationError::class => false];
    }
}