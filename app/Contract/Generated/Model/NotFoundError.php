<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class NotFoundError implements AdditionalPropertiesInterface
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
    public function definedProperties(): array
    {
        return ['code' => 'code', 'message' => 'message'];
    }
}