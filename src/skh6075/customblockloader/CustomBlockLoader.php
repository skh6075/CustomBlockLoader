<?php

declare(strict_types=1);

namespace skh6075\customblockloader;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ResourcePackStackPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\plugin\PluginBase;

final class CustomBlockLoader extends PluginBase implements Listener{

	private Experiments $experiments;

	private CustomBlockManager $customBlockManager;

	protected function onLoad() : void{
		$this->experiments = new Experiments([
			"data_driven_items" => true
		], true);
		$this->customBlockManager = CustomBlockManager::getInstance();
	}

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @private MONITOR
	 * @ignoreCancelled false
	 */
	public function onDataPacketReceiveEvent(DataPacketReceiveEvent $event): void{
		$packet = $event->getPacket();
		if($packet instanceof StartGamePacket){
			$packet->levelSettings->experiments = $this->experiments;
		}elseif($packet instanceof ResourcePackStackPacket){
			$packet->experiments = $this->experiments;
		}
	}
}