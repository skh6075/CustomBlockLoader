<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component;

use pocketmine\nbt\tag\CompoundTag;

interface BlockComponent{

	public function getName(): string;

	public function toComponent(): CompoundTag;
}