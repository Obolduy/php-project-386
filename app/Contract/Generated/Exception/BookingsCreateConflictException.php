<?php

namespace App\Contract\Generated\Exception;

class BookingsCreateConflictException extends ConflictException
{
    public function __construct(
        /**
         * @var \App\Contract\Generated\Model\SlotTakenError
         */
        private readonly \App\Contract\Generated\Model\SlotTakenError $slotTakenError,
        /**
         * @var \Symfony\Contracts\HttpClient\ResponseInterface
         */
        private readonly \Symfony\Contracts\HttpClient\ResponseInterface $response
    )
    {
        parent::__construct('Слот заняли между показом и отправкой формы.');
    }
    public function getSlotTakenError(): \App\Contract\Generated\Model\SlotTakenError
    {
        return $this->slotTakenError;
    }
    public function getResponse(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->response;
    }
}