<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class MeetingTypeInput implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
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
        return ['title' => 'title', 'description' => 'description', 'durationMinutes' => 'durationMinutes'];
    }
}