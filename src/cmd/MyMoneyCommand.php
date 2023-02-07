<?php

namespace ojy\EconomyAPI\cmd;

use ojy\EconomyAPI\EconomyAPI;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use ssss\utils\SSSSUtils;

final class MyMoneyCommand extends Command
{

    public function __construct()
    {
        parent::__construct('내돈', '내가 가진 돈을 확인합니다!', '/내돈', ['돈', 'money', 'mymoney']);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            return;
        }

        $session = EconomyAPI::getSession($sender);
        if ($session === null) {
            SSSSUtils::message($sender, '알 수 없는 오류입니다. 재접속해주세요!');
            return;
        }

        SSSSUtils::message($sender, '내가 가진 돈을 확인합니다!', '§l§g[꿀팜] §r§f');
        foreach (EconomyAPI::getInstance()->getCurrencies() as $currency) {
            if (isset($session->money[$currency->getName()])) {
                $money = $session->money[$currency->getName()];
                SSSSUtils::message($sender, "{$currency->getName()}: {$currency->format($money)}", '§l§gINFO §r§f');
            }
        }
    }
}