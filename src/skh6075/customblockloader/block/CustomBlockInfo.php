<?php

declare(strict_types=1);

namespace skh6075\customblockloader\block;

use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class CustomBlockInfo{

	/**
	 * @phpstan-var array<string, BlockComponent>
	 * @var BlockComponent[]
	 */
	private array $components = [];

	public function __construct(
		private string $identifier,
		private int $legacyId,
		private int $legacyMeta = 0
	){}

	public function getIdentifier(): string{
		return $this->identifier;
	}

	public function getLegacyId(): int{
		return $this->legacyId;
	}

	public function getLegacyMeta(): int{
		return $this->legacyMeta;
	}

	public function addComponent(BlockComponent $component): self{
		$this->components[$component->getName()] = $component;
		return $this;
	}

	public function getComponents(): array{
		return $this->components;
	}

	public function nbtSerialize(): CompoundTag{
		$nbt = CompoundTag::create();
		$componentNBT = CompoundTag::create();
		$nbt->setInt("molangVersion", 1);
		if(count($this->components) > 0){
			foreach($this->components as $component){
				$componentNBT->setTag($component->getName(), $component->toComponent());
			}
		}
		$nbt->setTag("components", $componentNBT);
		return $nbt;
	}
}