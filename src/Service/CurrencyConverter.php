<?php

namespace App\Service;

use App\Component\DTO\ConverterRequest;
use App\Component\DTO\ConverterResponse;
use App\Entity\Rate;
use App\Exception\CurrencyConverterException;
use Doctrine\Persistence\ManagerRegistry;

class CurrencyConverter
{
    private ManagerRegistry $registry;

    private SourceConfiguration $sourceConfiguration;

    public function __construct(ManagerRegistry $registry, SourceConfiguration $sourceConfiguration)
    {
        $this->registry            = $registry;
        $this->sourceConfiguration = $sourceConfiguration;
    }

    public function convert(ConverterRequest $request): ConverterResponse
    {
        $date         = $request->getDate();
        $currencyFrom = $request->getCurrencyFrom();
        $currencyTo   = $request->getCurrencyTo();

        if (!$this->sourceConfiguration->isMainCurrency($currencyFrom)) {
            $mainCurrency      = $this->sourceConfiguration->getMainCurrency();
            $dependentRateFrom = $this->getRate($mainCurrency, $currencyFrom, $date);
            $dependentRateTo   = $this->getRate($mainCurrency, $currencyTo, $date);

            $rate = $dependentRateTo->getRate() / $dependentRateFrom->getRate();

            return new ConverterResponse($request, $request->getAmount() / $rate);
        }

        $rate = $this->getRate($currencyFrom, $currencyTo, $date);

        return new ConverterResponse($request, $request->getAmount() / $rate->getRate());
    }

    private function getRate(string $currencyFrom, string $currencyTo, \DateTimeInterface $date): Rate
    {
        $rate = $this->registry->getRepository(Rate::class)->findOneBy([
            'mainCurrency'      => $currencyFrom,
            'secondaryCurrency' => $currencyTo,
            'date'              => $date,
        ]);

        if (!$rate instanceof Rate) {
            throw new CurrencyConverterException('Unable to convert, request data is not found.');
        }

        return $rate;
    }
}
