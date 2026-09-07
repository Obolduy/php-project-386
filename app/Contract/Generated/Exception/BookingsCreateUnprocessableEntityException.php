<?php

namespace App\Contract\Generated\Exception;

class BookingsCreateUnprocessableEntityException extends UnprocessableEntityException
{
    public function __construct(
        /**
         * @var \App\Contract\Generated\Model\ValidationError
         */
        private readonly \App\Contract\Generated\Model\ValidationError $validationError,
        /**
         * @var \Symfony\Contracts\HttpClient\ResponseInterface
         */
        private readonly \Symfony\Contracts\HttpClient\ResponseInterface $response
    )
    {
        parent::__construct('Данные не прошли проверку.');
    }
    public function getValidationError(): \App\Contract\Generated\Model\ValidationError
    {
        return $this->validationError;
    }
    public function getResponse(): \Symfony\Contracts\HttpClient\ResponseInterface
    {
        return $this->response;
    }
}