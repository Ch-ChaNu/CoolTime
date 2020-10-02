<?php

namespace chanu\task;

use chanu\CoolTime;
use pocketmine\scheduler\Task;

class CoolTimeTask extends Task
{
    /**
     * @var CoolTime
     */
    private $owner;

    /**
     * CoolTimeTask constructor.
     * @param CoolTime $owner
     */
    public function __construct(CoolTime $owner)
    {
        $this->owner = $owner;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick)
    {
        foreach ($this->owner->getServer()->getOnlinePlayers() as $onlinePlayer) {
            $this->owner->checkCool($onlinePlayer);
            $this->owner->sendTip($onlinePlayer);
        }
    }
}