<?php
class Pages  extends IndirectObject
{
	protected $pages = [];

	public function addPage(Page $page)
	{
		$this->pages[] = $page;
	}

	public function getMap()
	{
		return [
				'Type' => '/Pages',
				'Kids' => $this->pages,
				'Count' => count($this->pages),
		];
	}
}
