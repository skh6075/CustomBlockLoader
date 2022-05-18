<?php

declare(strict_types=1);

namespace skh6075\customblockloader\block;

use pocketmine\block\BlockIdentifier;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\BlockPaletteEntry;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use skh6075\customblockloader\component\BlockComponent;
use skh6075\customblockloader\CustomBlockManager;

class CustomBlockInfo{

	/**
	 * @phpstan-var array<string, BlockComponent>
	 * @var BlockComponent[]
	 */
	private array $components = [];

	private int $legacyId;

	public function __construct(private string $stringId){
		$this->legacyId = CustomBlockManager::getInstance()->getNextAvailableId();
	}

	public function getStringId(): string{
		return $this->stringId;
	}

	public function getBlockState(): CompoundTag{
		return CompoundTag::create()
			->setString("name", $this->stringId)
			->setTag("states", CompoundTag::create());
	}

	public function toBlockIdentifier(?string $tileClass = null): BlockIdentifier{
		return new BlockIdentifier($this->legacyId, 0, $this->legacyId, $tileClass);
	}

	public function toBlockPaletteEntry(): BlockPaletteEntry{
		return new BlockPaletteEntry($this->stringId, new CacheableNbt($this->nbtSerialize()));
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
		foreach($this->components as $component){
			$componentNBT->setTag($component->getName(), $component->toComponent());
		}
		$nbt->setTag("components", $componentNBT);
		return $nbt;
	}
}