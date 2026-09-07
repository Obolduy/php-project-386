<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class ValidationError implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
    /**
     * @var string
     */
    public string $code;
    /**
     * @var string
     */
    public string $message;
    /**
     * @var array<string, list<string>>
     */
    public iterable $fields;
    public function definedProperties(): array
    {
        return ['code' => 'code', 'message' => 'message', 'fields' => 'fields'];
    }
}