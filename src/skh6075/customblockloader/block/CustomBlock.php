<?php

declare(strict_types=1);

namespace skh6075\customblockloader\block;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\item\ItemFactory;
use pocketmine\item\Item;

class CustomBlock extends Block{

	public function __construct(
		BlockIdentifier $idInfo,
		string $name,
		BlockBreakInfo $breakInfo,
		private CustomBlockInfo $blockInfo
	){
		parent::__construct($idInfo, $name, $breakInfo);
	}

	public function getCustomBlockInfo() : CustomBlockInfo{
		return $this->blockInfo;
	}

	public function asItem() : Item{
		return ItemFactory::getInstance()->get(255 - $this->getId(), 0);
	}
}