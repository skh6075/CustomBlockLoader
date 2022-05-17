<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class FrictionComponent extends BlockComponent{

	public function __construct(private float $value = 0.1){
		parent::__construct("minecraft:friction");
	}

	public function isValid() : void{
		if($this->value > 1.0 || $this->value < 0.1){
			throw new InvalidArgumentException("The friction value must be greater than 0.1 and less than 1.0.");
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setFloat("value", $this->value);
	}
}