<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class MeetingType implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
    /**
     * @var int
     */
    public int $id;
    /**
     * @var string
     */
    public string $title;
    /**
     * @var string
     */
    public string $description;
    /**
     * @var int
     */
    public int $durationMinutes;
    public function definedProperties(): array
    {
        return ['id' => 'id', 'title' => 'title', 'description' => 'description', 'durationMinutes' => 'durationMinutes'];
    }
}