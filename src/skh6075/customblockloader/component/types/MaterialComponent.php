<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use InvalidArgumentException;
use pocketmine\nbt\tag\CompoundTag;
use skh6075\customblockloader\component\BlockComponent;

class MaterialComponent extends BlockComponent{

	private CompoundTag $mergeComponent;

	private bool $used_newInstance = false;

	public function __construct(
		private string $texture,
		private string $render_method = "opaque",
		private bool $ambient_occlusion = true,
		private bool $face_dimming = true
	){
		parent::__construct("minecraft:material_instances");
		$this->mergeComponent = CompoundTag::create();
	}

	public function isValid() : void{
		if(!in_array($this->render_method, ["opaque", "double_sided", "blend", "alpha_test"], true)){
			throw new InvalidArgumentException("The render method to use. Must be one of 'opaque', 'double_sided', 'blend', or 'alpha_test'.");
		}
	}

	public function toComponent() : CompoundTag{
		$this->mergeComponent->setTag(match($this->used_newInstance){
			true => "NewInstance",
			false => "*"
		}, CompoundTag::create()
			->setString("texture", $this->texture)
			->setString("render_method", $this->render_method)
			->setByte("ambient_occlusion", $this->ambient_occlusion ? 1 : 0)
			->setByte("face_dimming", $this->face_dimming ? 1 : 0)
		);
		return CompoundTag::create()->setTag($this->getName(), $this->mergeComponent);
	}
}