<?php

namespace ojy\EconomyAPI\listener;

use ojy\EconomyAPI\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class EventListener implements Listener
{

    public function onPlayerLogin(PlayerLoginEvent $event): void
    {
        EconomyAPI::createSession($event->getPlayer());
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void
    {
        EconomyAPI::removeSession($event->getPlayer());
    }
}