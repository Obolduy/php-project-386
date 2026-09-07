<?php

namespace App\Contract\Generated\Model;

use App\Contract\Generated\Runtime\AdditionalAndPatternProperties;
use App\Contract\Generated\Runtime\AdditionalPropertiesInterface;
class Booking implements AdditionalPropertiesInterface
{
    use AdditionalAndPatternProperties;
    /**
     * @var int
     */
    public int $id;
    /**
     * @var int
     */
    public int $meetingTypeId;
    /**
     * @var \DateTime
     */
    public \DateTime $startsAt;
    /**
     * @var \DateTime
     */
    public \DateTime $endsAt;
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
        return ['id' => 'id', 'meetingTypeId' => 'meetingTypeId', 'startsAt' => 'startsAt', 'endsAt' => 'endsAt', 'guestName' => 'guestName', 'guestEmail' => 'guestEmail'];
    }
}