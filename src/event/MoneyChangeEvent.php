<?php

namespace ojy\EconomyAPI\event;

use ojy\EconomyAPI\currency\Currency;
use ojy\EconomyAPI\session\EconomySession;
use pocketmine\event\Event;

final class MoneyChangeEvent extends Event
{

    public function __construct(protected EconomySession $session, protected Currency $currency)
    {
    }

    public function getSession(): EconomySession
    {
        return $this->session;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }
}