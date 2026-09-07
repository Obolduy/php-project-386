<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class SlotList implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
    /**
     * @var string
     */
    public string $timezone;
    /**
     * @var list<Slot>
     */
    public array $slots;
    public function definedProperties(): array
    {
        return ['timezone' => 'timezone', 'slots' => 'slots'];
    }
}