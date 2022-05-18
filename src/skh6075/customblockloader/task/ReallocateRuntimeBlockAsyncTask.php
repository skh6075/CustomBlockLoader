<?php

declare(strict_types=1);

namespace skh6075\customblockloader\task;

use pocketmine\scheduler\AsyncTask;
use skh6075\customblockloader\CustomBlockManager;
use function unserialize;

class ReallocateRuntimeBlockAsyncTask extends AsyncTask{

	public function __construct(private string $blocks) {}

	public function onRun(): void {
		$blocks = unserialize($this->blocks);
		foreach($blocks as $identifier => $block){
			CustomBlockManager::getInstance()->register($block);
		}
		CustomBlockManager::getInstance()->registerCustomRuntimeMappings();
	}
}