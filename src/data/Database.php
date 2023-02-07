<?php

namespace ojy\EconomyAPI\data;

use ojy\EconomyAPI\EconomyAPI;
use pocketmine\player\Player;

final class Database
{

    public array $db;

    public function __construct()
    {
        $this->db = file_exists($this->getDataFilePath()) ? json_decode(file_get_contents($this->getDataFilePath()), true) : [];
    }

    public function hasAccount(Player|string $player): bool
    {
        $player = strtolower($player instanceof Player ? $player->getName() : $player);
        return isset($this->db[$player]);
    }

    public function getDataFilePath(): string
    {
        return EconomyAPI::getInstance()->getDataFolder() . 'Money.json';
    }

    public function save(): void
    {
        file_put_contents($this->getDataFilePath(), json_encode($this->db));
    }
}