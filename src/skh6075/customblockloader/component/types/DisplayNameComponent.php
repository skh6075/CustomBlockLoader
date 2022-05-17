<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class DisplayNameComponent extends BlockComponent{

	public function __construct(private string $name){
		parent::__construct("minecraft:display_name");
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setString($this->getName(), $this->name);
	}
}