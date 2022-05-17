<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class MapColorComponent extends BlockComponent{

	public function __construct(private string $color = "#FFFFFF"){
		parent::__construct("minecraft:map_color");
	}

	public function isValid() : void{
		if(!preg_match('/^#[a-f0-9]{6}$/i', $this->color)){
			if(!preg_match('/^[a-f0-9]{6}$/i', $this->color)){
				throw new \InvalidArgumentException("color value can only use hex color code");
			}
			$this->color = "#$this->color";
		}
	}

	public function toComponent() : CompoundTag{
		return CompoundTag::create()->setString($this->getName(), $this->color);
	}
}