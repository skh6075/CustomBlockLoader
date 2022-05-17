<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class BlockLightEmissionComponent extends BlockComponent{

	public function __construct(private float $value = 0.0){
		parent::__construct("minecraft:block_light_emission");
	}

	public function isValid() : void{
		if($this->value > 1.0 || $this->value < 0){
			throw new InvalidArgumentException("The emission value must be greater than 0.0 and less than 1.0");
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setFloat($this->getName(), $this->value);
	}
}