<?php

namespace ojy\EconomyAPI\currency;

final class CurrencyGold extends Currency
{

    public function getName(): string
    {
        return '골드';
    }

    public function getSymbol(): string
    {
        return '§l§gG';
    }

    public function getDefaultMoney(): int
    {
        return 100000;
    }

    public function canTransaction(): bool
    {
        return true;
    }
}