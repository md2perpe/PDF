<?php
class Catalog  extends IndirectObject
{
	protected $pages;

	public function setPages(Pages $pages)
	{
		$this->pages = $pages;
	}

	public function getMap()
	{
		return [
				'Type' => '/Catalog',
				'Pages' => $this->pages,
		];
	}
}
