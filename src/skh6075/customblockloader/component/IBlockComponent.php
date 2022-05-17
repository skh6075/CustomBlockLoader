<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component;

use pocketmine\nbt\tag\CompoundTag;

interface IBlockComponent{

	public function getName(): string;

	public function isValid(): void;

	public function toComponent(): CompoundTag;
}