<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class UnwalkableComponent implements BlockComponent{

	public function __construct(private bool $value = true){}

	public function getName(): string{
		return "minecraft:unwalkable";
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()
			->setByte($this->getName(), $this->value ? 1 : 0);
	}
}