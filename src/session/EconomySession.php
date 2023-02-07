<?php

namespace ojy\EconomyAPI\session;

use ojy\EconomyAPI\currency\Currency;
use ojy\EconomyAPI\EconomyAPI;
use ojy\EconomyAPI\event\MoneyChangeEvent;

final class EconomySession
{

    public array $money;

    public function __construct(public string $name)
    {
        if (EconomyAPI::$database->hasAccount($this->name)) {
            foreach (EconomyAPI::$database->db[$name] as $currencyName => $money) {
                $this->money[$currencyName] = $money;
            }
        }
        foreach (EconomyAPI::getInstance()->getCurrencies() as $currency) {
            if (!isset($this->money[$currency->getName()])) {
                $this->money[$currency->getName()] = $currency->getDefaultMoney();
            }
        }
    }

    public function getMoney(Currency $currency = null): int
    {
        if ($currency === null) {
            $currency = EconomyAPI::getInstance()->getDefaultCurrency();
        }

        return $this->money[$currency->getName()] ?? -1;
    }

    public function reduceMoney(float $amount, Currency $currency = null): bool
    {
        if ($currency === null) {
            $currency = EconomyAPI::getInstance()->getDefaultCurrency();
        }

        if ($this->getMoney($currency) >= $amount) {
            $this->money[$currency->getName()] -= $amount;

            (new MoneyChangeEvent($this, $currency))->call();

            $this->synchronize();
            return true;
        }
        return false;
    }

    public function addMoney(float $amount, Currency $currency = null): void
    {
        if ($currency === null) {
            $currency = EconomyAPI::getInstance()->getDefaultCurrency();
        }
        if (!isset($this->money[$currency->getName()])) {
            return;
        }
        $this->money[$currency->getName()] += $amount;

        (new MoneyChangeEvent($this, $currency))->call();

        $this->synchronize();
    }

    public function setMoney(float $amount, Currency $currency = null): void
    {
        if ($currency === null) {
            $currency = EconomyAPI::getInstance()->getDefaultCurrency();
        }
        if (!isset($this->money[$currency->getName()])) {
            return;
        }
        if ($amount < 0) {
            $amount = 0;
        }
        if ($amount > 99999999999) {
            $amount = 99999999999;
        }
        $this->money[$currency->getName()] = $amount;

        (new MoneyChangeEvent($this, $currency))->call();

        $this->synchronize();
    }

    public function synchronize(bool $save = false): void
    {
        EconomyAPI::$database->db[$this->name] = $this->money;

        if ($save) {
            EconomyAPI::$database->save();
        }
    }
}