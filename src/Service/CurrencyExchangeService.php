<?php

namespace App\Service;

use App\Entity\Currency;
use App\Exception\APIException;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class CurrencyExchangeService
{
    private const EXCHANGE_API_URL = 'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return void
     * @throws APIException
     * @throws GuzzleException
     * @throws Exception
     */
    public function exchangeAPI(): void
    {
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', self::EXCHANGE_API_URL);
        } catch (ClientException $e) {
            throw new APIException($e->getResponse()->getBody(true), $e->getCode());
        }
        $xml = $response->getBody()->getContents();
        $this->XMLConversion($xml);
    }

    /**
     * @param string $xml
     * @return bool
     * @throws Exception
     */
    public function XMLConversion(string $xml): bool
    {
        $xml = new \SimpleXMLElement($xml);

        $date = new DateTime(DateTime::createFromFormat("Y-m-d", (string)$xml->Cube->Cube['time'])->format('Y-m-d H:i:s'));

        foreach ($xml->Cube->Cube->Cube as $cube) {
            $currency = $this->entityManager->getRepository(Currency::class)->findOneBy(['currency' => $cube['currency']]);
            if (!$currency) {
                $currency = new Currency();
            }
            $currency->setCurrency($cube['currency']);
            $currency->setExchangeRate($cube['rate']);
            $currency->setUpdatedAt($date);

            $this->entityManager->persist($currency);
            $this->entityManager->flush();
        }
        return true;
    }
}