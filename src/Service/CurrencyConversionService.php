<?php

namespace App\Service;

use App\Entity\Currency;
use App\Exception\DataMismatchException;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyConversionService implements CurrencyConversionServiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $fromCurrency
     * @param string $toCurrency
     * @param int $amount
     * @return array
     * @throws DataMismatchException
     */
    public function convertCurrency(string $fromCurrency, string $toCurrency, int $amount): array
    {
        $fromCurrencyEntity = $this->entityManager->getRepository(Currency::class)->findOneBy(['currency' => $fromCurrency]);
        $toCurrencyEntity = $this->entityManager->getRepository(Currency::class)->findOneBy(['currency' => $toCurrency]);

        if ($fromCurrencyEntity && $toCurrencyEntity) {
            $conversionRate  = $fromCurrencyEntity->getExchangeRate() / $toCurrencyEntity->getExchangeRate();
            $convertedAmount = round ($amount / $conversionRate, 2);

            return [
                'updated_time' => $fromCurrencyEntity->getUpdatedAt()->format('Y-m-d H:i:s'),
                'result' => $convertedAmount
            ];
        }

        throw new DataMismatchException();
    }
}