<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class DestroyTimeComponent extends BlockComponent{

	public function __construct(private float $value = 0.0){
		parent::__construct("minecraft:destroy_time");
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setFloat("value", $this->value);
	}
}