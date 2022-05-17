<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class AimCollisionComponent extends BlockComponent{

	public function __construct(
		private Vector3 $origin,
		private Vector3 $size
	){
		parent::__construct("minecraft:aim_collision");
	}

	public function isValid() : void{
		if(
			($this->size->x > 8 || $this->size->x < -8) ||
			($this->size->z > 8 || $this->size->z < -8)
		){
			throw new InvalidArgumentException("size offset must be greater than -8 and less than 8");
		}
		if(
			($this->origin->x < -16 || $this->origin->x > 16) ||
			($this->origin->z < -16 || $this->origin->z > 16) ||
			($this->origin->y < -16 || $this->origin->y > 16)
		){
			throw new InvalidArgumentException("The origin offset must be specified as a value between -16 and 16");
		}
	}

	public function toComponent() : CompoundTag{
		// I don't know.... plz help me !!
		return CompoundTag::create()->setByte("value", 0);
	}
}