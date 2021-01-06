<?php

namespace App\Service;

use App\Component\DTO\ConversionRate;
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

            return new ConverterResponse($request, $request->getAmount() * $rate);
        }

        $rate = $this->getRate($currencyFrom, $currencyTo, $date);

        return new ConverterResponse($request, $request->getAmount() * $rate->getRate());
    }

    private function getRate(string $currencyFrom, string $currencyTo, \DateTimeInterface $date): ConversionRate
    {
        if ($currencyFrom === $currencyTo) {
            return new ConversionRate(1, false);
        }

        $rate = $this->registry->getRepository(Rate::class)->findOneBy([
            'mainCurrency'      => $currencyFrom,
            'secondaryCurrency' => $currencyTo,
            'date'              => $date,
        ]);

        $reverseRate = null;

        if (!$rate instanceof Rate) {
            $reverseRate = $this->registry->getRepository(Rate::class)->findOneBy([
                'mainCurrency'      => $currencyTo,
                'secondaryCurrency' => $currencyFrom,
                'date'              => $date,
            ]);
        }

        if ($reverseRate instanceof Rate) {
            return new ConversionRate($reverseRate->getRate(), true);
        }

        if (!$rate instanceof Rate && null === $reverseRate) {
            throw new CurrencyConverterException('Unable to convert, request data is not found.');
        }

        return new ConversionRate($rate->getRate(), false);
    }
}
