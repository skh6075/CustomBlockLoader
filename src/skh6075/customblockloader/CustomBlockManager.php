<?php

declare(strict_types=1);

namespace skh6075\customblockloader;

use Closure;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\utils\SingletonTrait;
use SplFixedArray;

final class CustomBlockManager{
	use SingletonTrait;

	public const BLOCK_MAP_SIZE = 2048 << Block::INTERNAL_METADATA_BITS;

	public static function getInstance() : CustomBlockManager{
		return self::$instance ??= new self;
	}

	private BlockFactory $blockFactory;

	public function __construct(){
		$this->blockFactory = BlockFactory::getInstance();

		Closure::bind( //HACK: Closure bind hack to access inaccessible members
			closure: static function(BlockFactory $factory): void{
				$factory->fullList->setSize(CustomBlockManager::BLOCK_MAP_SIZE);
				$factory->mappedStateIds->setSize(CustomBlockManager::BLOCK_MAP_SIZE);
				$factory->light = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, 0));
				$factory->lightFilter = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, 1));
				$factory->blocksDirectSkyLight = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, false));
				$factory->blastResistance = SplFixedArray::fromArray(array_fill(0, CustomBlockManager::BLOCK_MAP_SIZE, 0.0));
			},
			newThis: null,
			newScope: BlockFactory::class
		)($this->blockFactory);
	}
}