<?php

namespace App\Exceptions;

use Exception;

class ProductAlreadyFavoritedException extends Exception
{
    protected $message = 'Este produto já está nos favoritos.';

    public function __construct($message = null)
    {
        parent::__construct($message ?? $this->message);
    }
}

