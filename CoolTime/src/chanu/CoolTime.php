<?php

namespace chanu;

use chanu\task\CoolTimeTask;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class CoolTime extends PluginBase implements Listener
{

    /**
     * @var array
     */
    public $time = [];

    public function onEnable()
    {
        Server::getInstance()->getPluginManager()->registerEvents($this, $this);
        $this->getScheduler()->scheduleRepeatingTask(new CoolTimeTask($this), 20);
    }

    /**
     * @param EntityDamageByEntityEvent $event
     */
    public function onDamage(EntityDamageByEntityEvent $event)
    {
        $entity = $event->getEntity();
        $damager = $event->getDamager();

        if(!$entity instanceof Player or !$damager instanceof Player) return;
        if($event->isCancelled()) return;
        $this->time[strtolower($damager->getName())] = time();
        $this->time[strtolower($entity->getName())] = time();
    }

    /**
     * @param PlayerLoginEvent $event
     */
    public function onLogin(PlayerLoginEvent $event)
    {
        if(!isset($this->time[strtolower($event->getPlayer()->getName())])) {
            $this->time[strtolower($event->getPlayer()->getName())] = null;
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onProcess(PlayerCommandPreprocessEvent $event)
    {
        if($this->time[strtolower($event->getPlayer()->getName())] == null) return;
        if(isset($this->time[strtolower($event->getPlayer()->getName())])){
            $event->setCancelled();
            return;
        }
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        if($this->time[strtolower($event->getPlayer()->getName())] == null) return;
        if(isset($this->time[strtolower($event->getPlayer()->getName())])){
            $event->getPlayer()->kill();
            unset($this->time[strtolower($event->getPlayer()->getName())]);
            return;
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function checkCool(Player $player) : bool
    {
        $name = strtolower($player->getName());
        if($this->time[strtolower($player->getName())] == null) return false;
        if((5 - (time() - $this->time[$name])) <= 0) {
            $this->time[strtolower($player->getName())] = null;
            return true;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function sendTip(Player $player) : bool
    {
        $name = strtolower($player->getName());
        if($this->time[strtolower($player->getName())] == null) return false;
        $player->sendTip('전투중입니다. 남은시간 : ' . (5 - (time() - $this->time[$name])) . '초');
        return true;
    }
}