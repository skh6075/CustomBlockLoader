<?php

declare(strict_types=1);

namespace skh6075\customblockloader\component\types;

use skh6075\customblockloader\component\BlockComponent;

class ExplosionResistanceComponent extends BlockComponent{

	public function __construct(private float $value = 0.0){
		parent::__construct("")
	}
}