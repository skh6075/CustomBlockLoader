<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class BreathabilityComponent extends BlockComponent{

	public function __construct(private string $value = "solid"){
		parent::__construct("minecraft:breathability");
	}

	public function isValid() : void{
		if(!in_array($this->value, ["solid", "air"], true)){
			throw new InvalidArgumentException("The breathability value is available only for solid or air.");
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setString($this->getName(), $this->value);
	}
}