<?php

namespace skh6075\customblockloader\component;

abstract class BlockComponent implements IBlockComponent{

	private string $identifier;

	public function __construct(string $identifier){
		$this->identifier = $identifier;
		$this->isValid();
	}

	public function getName() : string{
		return $this->identifier;
	}

	public function isValid() : void{
		// TODO: Implement isValid() method.
	}
}