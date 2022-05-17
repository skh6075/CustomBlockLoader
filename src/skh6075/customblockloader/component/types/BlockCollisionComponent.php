<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class BlockCollisionComponent extends BlockComponent{

	public function __construct(
		private Vector3 $origin,
		private Vector3 $size
	){
		parent::__construct("minecraft:block_collision");
	}

	public function isValid() : void{
		if(
			($this->origin->x < -8 || $this->origin->x > 8) ||
			($this->origin->z < -8 || $this->origin->z > 8) ||
			($this->origin->y < 0 || $this->origin->y > 1)
		){
			throw new InvalidArgumentException("The origin offset must be specified as a value between -8 and 8, and the y offset must be greater than 0 and less than 1.");
		}
		if(
			($this->size->x > 16 || $this->size->x < -16) ||
			($this->size->z > 16 || $this->size->z < -16) ||
			($this->size->y > 16 || $this->size->y < -16) ||
			($this->size->x !== $this->size->y || $this->size->y !== $this->size->z || $this->size->z !== $this->size->x)
		){
			throw new InvalidArgumentException("size offset must be greater than -16 and less than 16, and all offsets must have the same value");
		}
	}

	public function toComponent() : CompoundTag{
		// I don't know.... plz help me !!
		return CompoundTag::create()->setByte($this->getName(), 0);
	}
}