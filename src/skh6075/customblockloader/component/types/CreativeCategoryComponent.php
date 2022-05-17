<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class CreativeCategoryComponent extends BlockComponent{
	private const CATEGORIES = [
		"all",
		"commands",
		"construction",
		"equipment",
		"items",
		"nature",
		"none"
	];

	public function __construct(
		private string $category = "all",
		private ?string $group = null
	){
		parent::__construct("minecraft:creative_category");
	}

	public function isValid() : void{
		if(!in_array($this->category, self::CATEGORIES, true)){
			throw new InvalidArgumentException("Only " . implode(",", self::CATEGORIES) . " can be entered in the category.");
		}
	}

	public function toComponent() : CompoundTag{
		$nbt = CompoundTag::create()->setString("category", $this->category);
		if($this->group !== null){
			$nbt->setString("group", $this->group);
		}
		return CompoundTag::create()->setTag("value", $nbt);
	}
}