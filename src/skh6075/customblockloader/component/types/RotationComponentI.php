<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use skh6075\customblockloader\component\BlockComponent;
use function round;

class RotationComponentI extends BlockComponent{

	public function __construct(private Vector3 $rotation){
		parent::__construct("minecraft:rotation");
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()
			->setTag($this->getName(), new ListTag([
				round($this->rotation->x, 1),
				round($this->rotation->y, 1),
				round($this->rotation->z, 1)
			]));
	}

	public function isValid() : void{
		$rotation = $this->rotation;
		if(
			($rotation->x > 16.0 || $rotation->x < -16.0) ||
			($rotation->y > 16.0 || $rotation->y < -16.0) ||
			($rotation->z > 16.0 || $rotation->z < -16.0)
		){
			throw new InvalidArgumentException("rotation must be greater than -16 or less than 16.");
		}
	}
}