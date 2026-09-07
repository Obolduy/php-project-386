<?php

namespace App\Contract\Generated\Exception;

class BookingsCreateNotFoundException extends NotFoundException
{
    public function __construct(
        /**
         * @var \App\Contract\Generated\Model\NotFoundError
         */
        private readonly \App\Contract\Generated\Model\NotFoundError $notFoundError,
        /**
         * @var \Symfony\Contracts\HttpClient\ResponseInterface
         */
        private readonly \Symfony\Contracts\HttpClient\ResponseInterface $response
    )
    {
        parent::__construct('Ресурс не найден.');
    }
    public function getNotFoundError(): \App\Contract\Generated\Model\NotFoundError
    {
        return $this->notFoundError;
    }
    public function getResponse(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->response;
    }
}