<?php

namespace ojy\EconomyAPI\currency;

final class CurrencyCash extends Currency
{

    public function getName(): string
    {
        return '캐쉬';
    }

    public function getSymbol(): string
    {
        return '§l§eC';
    }

    public function getDefaultMoney(): int
    {
        return 0;
    }

    public function canTransaction(): bool
    {
        return false;
    }
}