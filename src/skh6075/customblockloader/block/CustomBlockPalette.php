<?php

declare(strict_types=1);

namespace skh6075\customblockloader\block;

use Closure;
use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\utils\SingletonTrait;

final class CustomBlockPalette{
	use SingletonTrait;

	public static function getInstance() : CustomBlockPalette{
		return self::$instance ??= new self;
	}

	private RuntimeBlockMapping $runtimeBlockMapping;

	private Closure $overrideClosure;

	/**
	 * @phpstan-var array<int, CompoundTag>
	 * @var CompoundTag[]
	 */
	public array $originStates = [];

	/**
	 * @phpstan-var array<int, CompoundTag>
	 * @var CompoundTag[]
	 */
	public array $customStates = [];

	private function __construct(){
		$this->runtimeBlockMapping = RuntimeBlockMapping::getInstance();
		Closure::bind(
			closure: function(RuntimeBlockMapping $mapping): void{
				$this->originStates = $mapping->bedrockKnownStates;
			},
			newThis: null,
			newScope: RuntimeBlockMapping::class
		)($this->runtimeBlockMapping);
		$this->overrideClosure = function(): void{ };
	}

	public function getOriginStates(): array{
		return $this->originStates;
	}

	public function getCustomStates(): array{
		return $this->customStates;
	}

	public function registerState(CompoundTag $state): void{
		$this->isValid($state);
		$this->reallocate($state);
		$this->customStates[] = $state;
	}

	private function isValid(CompoundTag $nbt): void{
		if($nbt->getString("name", "") === ""){
			throw new InvalidArgumentException("Block state must contain a StringTag called 'name'");
		}
		if($nbt->getCompoundTag("states") === null){
			throw new InvalidArgumentException("Block state must contain a CompoundTag called 'states'");
		}
	}

	private function reallocate(CompoundTag $state): void{
		$states = [$state->getString("name") => [$state]];
		foreach($this->originStates as $originState){
			$states[$originState->getString("name")][] = $originState;
		}
		$names = array_keys($states);
		usort($names, static fn(string $a, string $b) => strcmp(self::toFNV164($a), self::toFNV164($b)));
		$newStates = [];
		foreach($names as $name){
			foreach($states[$name] as $resource){
				$newStates[] = $resource;
			}
		}
		$this->originStates = $newStates;
		Closure::bind(
			closure: static function(RuntimeBlockMapping $mapping, array $states): void{
				$mapping->bedrockKnownStates = $states;
			},
			newThis: null,
			newScope: RuntimeBlockMapping::class
		)(RuntimeBlockMapping::getInstance(), $newStates);
	}

	private static function toFNV164(string $name) : string{
		return hash("fnv164", $name);
	}
}