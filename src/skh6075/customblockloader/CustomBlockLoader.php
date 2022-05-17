<?php

declare(strict_types=1);

namespace skh6075\customblockloader;

use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;

final class CustomBlockLoader extends PluginBase implements Listener{

	private CustomBlockManager $customBlockManager;

	protected function onLoad() : void{
		$this->customBlockManager = CustomBlockManager::getInstance();
	}

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}
}