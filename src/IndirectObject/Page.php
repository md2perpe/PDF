<?php
class Page  extends IndirectObject
{
	protected $parent;

	public function __construct(Pages $parent)
	{
		$this->parent = $parent;
	}

	public function getMap()
	{
		return [
				'Type' => '/Page',
				'Parent' => $this->parent,
				'Resources' => '<< >>',
				'MediaBox' => '[ 0 0 1000 1000 ]',
		];
	}
}
