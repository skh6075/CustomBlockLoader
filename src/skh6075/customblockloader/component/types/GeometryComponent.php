<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class GeometryComponent extends BlockComponent{

	public function __construct(){
		parent::__construct("minecraft:geometry");
	}

	public function toComponent() : CompoundTag{
		//Block geometry only supports cube...
		return CompoundTag::create()->setString("value", "cube");
	}
}