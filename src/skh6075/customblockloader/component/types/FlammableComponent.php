<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class FlammableComponent extends BlockComponent{

	public function __construct(
		private int $burn_odds = 0,
		private int $flame_odds = 0
	){
		parent::__construct("minecraft:flammable");
	}

	public function isValid() : void{
		if($this->burn_odds < 0 || $this->flame_odds < 0){
			throw new InvalidArgumentException("The value of burn_odds and flame_odds must be greater than or equal to 0");
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()
			->setTag("value", CompoundTag::create()
				->setInt("burn_odds", $this->burn_odds)
				->setInt("flame_odds", $this->flame_odds)
			);
	}
}