<?php

declare(strict_types=1);

namespace skh6075\customblockloader;

use kim\present\utils\identifier\IdentifierUtils;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\Experiments;
use pocketmine\plugin\PluginBase;
use RuntimeException;

final class CustomBlockLoader extends PluginBase implements Listener{

	private Experiments $experiments;

	private CustomBlockManager $customBlockManager;

	protected function onEnable() : void{
		if(!class_exists(IdentifierUtils::class)){
			throw new RuntimeException("IdentifierUtils virion not found. You can download it here https://github.com/presentkim-pm/identifier-utils");
		}

		$this->customBlockManager = CustomBlockManager::getInstance();
		$this->customBlockManager->registerCustomRuntimeMappings();
		$this->customBlockManager->addWorkerInitHook();

		$this->experiments = new Experiments([
			"data_driven_items" => true
		], true);

		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	/**
	 * @priority MONITOR
	 * @ignoreCancelled false
	 */
	public function onDataPacketSendEvent(DataPacketSendEvent $event): void{
		$customBlockPalette = $this->customBlockManager->getBlockPalettes();
		$customBlockCount = count($customBlockPalette);
		if($customBlockCount === 0){
			return;
		}
		foreach($event->getPackets() as $packet){
			if($packet instanceof StartGamePacket){
				$packet->levelSettings->experiments = $this->experiments;
				if(empty($packet->blockPalette)){
					$packet->blockPalette = $customBlockPalette;
				}else{
					$packet->blockPalette = array_merge($packet->blockPalette, $customBlockPalette);
				}
			}
		}
	}
}