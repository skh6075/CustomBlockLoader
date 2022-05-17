<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class BlockLightFilterComponent extends BlockComponent{

	public function __construct(private int $value = 0){
		parent::__construct("minecraft:block_light_filter");
	}

	public function isValid() : void{
		if($this->value > 15 || $this->value < 0){
			throw new InvalidArgumentException("The light value must be greater than 0 and less than 15.");
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setInt($this->getName(), $this->value);
	}
}