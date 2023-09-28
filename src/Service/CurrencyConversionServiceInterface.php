<?php

namespace App\Service;

interface CurrencyConversionServiceInterface
{
    public function convertCurrency(string $fromCurrency, string $toCurrency, int $amount): array;
}