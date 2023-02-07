<?php

namespace ojy\EconomyAPI\currency;

abstract class Currency
{
    public const BIG_ORDER = ['', '만 ', '억 ', '조 ', '경 '];

    abstract public function getName(): string;

    abstract public function getSymbol(): string;

    abstract public function getDefaultMoney(): int;

    abstract public function canTransaction(): bool;

    public function format(int|float $money): string
    {
        $str = '';
        for ($i = count(self::BIG_ORDER) - 1; $i >= 0; --$i) {
            $unit = 10000 ** $i;
            $part = floor($money / $unit);
            if ($part > 0) {
                $str .= $part . self::BIG_ORDER[$i];
            }
            $money %= $unit;
        }
        if ($str === '') {
            $str = '0';
        }
        return $str . $this->getSymbol() . '§r§f';
    }
}