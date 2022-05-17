<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class LootComponent extends BlockComponent{

	public function __construct(
		private string $json,
		private string $behavior_path
	){
		parent::__construct("minecraft:loot");
	}

	public function isValid() : void{
		if(pathinfo($this->json, PATHINFO_EXTENSION) !== "json"){
			throw new InvalidArgumentException("It is not a json file format.");
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setString("value", "$this->behavior_path/$this->json");
	}
}