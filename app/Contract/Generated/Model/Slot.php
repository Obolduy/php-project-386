<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class Slot implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
    /**
     * @var \DateTime
     */
    public \DateTime $startsAt;
    /**
     * @var \DateTime
     */
    public \DateTime $endsAt;
    public function definedProperties(): array
    {
        return ['startsAt' => 'startsAt', 'endsAt' => 'endsAt'];
    }
}