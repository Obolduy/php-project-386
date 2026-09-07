<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class BookingInput implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
    /**
     * @var int
     */
    public int $meetingTypeId;
    /**
     * @var \DateTime
     */
    public \DateTime $startsAt;
    /**
     * @var string
     */
    public string $guestName;
    /**
     * @var string
     */
    public string $guestEmail;
    public function definedProperties(): array
    {
        return ['meetingTypeId' => 'meetingTypeId', 'startsAt' => 'startsAt', 'guestName' => 'guestName', 'guestEmail' => 'guestEmail'];
    }
}