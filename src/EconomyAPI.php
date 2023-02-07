<?php

namespace ojy\EconomyAPI;

use ojy\EconomyAPI\cmd\MyMoneyCommand;
use ojy\EconomyAPI\currency\Currency;
use ojy\EconomyAPI\currency\CurrencyCash;
use ojy\EconomyAPI\currency\CurrencyGold;
use ojy\EconomyAPI\data\Database;
use ojy\EconomyAPI\listener\EventListener;
use ojy\EconomyAPI\session\EconomySession;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;

final class EconomyAPI extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public static Database $database;

    protected function onEnable(): void
    {
        self::$database = new Database();

        $this->registerCurrency(new CurrencyGold(), true);
        $this->registerCurrency(new CurrencyCash());

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function (): void {
            self::$database->save();
        }), 6000, 6000);

        $this->getServer()->getCommandMap()->registerAll('EconomyAPI', [
            new MyMoneyCommand()
        ]);
    }

    protected function onDisable(): void
    {
        foreach (self::$sessions as $session) {
            $session->synchronize();
        }
        self::$database->save();
    }

    /** @var Currency[] */
    protected array $currencies = [];

    public Currency $defaultCurrency;

    public function registerCurrency(Currency $currency, bool $default = false): void
    {
        $this->currencies[$currency->getName()] = $currency;
        if ($default) {
            $this->defaultCurrency = $currency;
        }
    }

    public function getDefaultCurrency(): Currency
    {
        return $this->defaultCurrency;
    }

    public function getCurrency(string $currencyName): ?Currency
    {
        return $this->currencies[$currencyName] ?? null;
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }

    /** @var EconomySession[] */
    public static array $sessions = [];

    public static function createSession(Player|string $player): EconomySession
    {
        $player = strtolower($player instanceof Player ? $player->getName() : $player);
        if (!isset(self::$sessions[$player])) {
            return self::$sessions[$player] = new EconomySession($player);
        }
        return self::$sessions[$player];
    }

    public static function getSession(Player|string $player): ?EconomySession
    {
        $player = strtolower($player instanceof Player ? $player->getName() : $player);
        return self::$sessions[$player] ?? null;
    }

    public static function removeSession(Player|string $player): void
    {
        $player = strtolower($player instanceof Player ? $player->getName() : $player);
        if (isset(self::$sessions[$player])) {
            self::$sessions[$player]->synchronize();
            unset(self::$sessions[$player]);
        }
    }

    public function getMoney(Player|string $player, Currency $currency = null): float
    {
        if (!self::$database->hasAccount($player)) {
            return -1;
        }
        return self::createSession($player)->getMoney($currency);
    }

    public const
        CANNOT_FIND_ACCOUNT = 0,
        NOT_ENOUGH_MONEY = 1,
        SUCCESS = 2;

    public function reduceMoney(Player|string $player, float $amount, Currency $currency = null): int
    {
        if (!self::$database->hasAccount($player)) {
            return self::CANNOT_FIND_ACCOUNT;
        }
        if (self::createSession($player)->reduceMoney($amount, $currency)) {
            return self::SUCCESS;
        }
        return self::NOT_ENOUGH_MONEY;
    }

    public function addMoney(Player|string $player, float $amount, Currency $currency = null): bool
    {
        if (!self::$database->hasAccount($player)) {
            return false;
        }
        self::createSession($player)->addMoney($amount, $currency);
        return true;
    }
}