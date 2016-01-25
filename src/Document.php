<?php
class Document
{
	protected $xrefOffset;

	protected $catalog;
	protected $currentPages;
	protected $currentPage;

	protected $objectManager;

	public function __construct(IndirectObjectManager $objectManager)
	{
		$this->objectManager = $objectManager;

		$catalog = new Catalog();
		$objectManager->register($catalog);

		$pages = new Pages();
		$objectManager->register($pages);

		$page = new Page($pages);
		$objectManager->register($page);

		$catalog->setPages($pages);
		$pages->addPage($page);

		$this->catalog = $catalog;
		$this->currentPages = $pages;
		$this->currentPage = $page;
	}


	public function output(Writer $writer)
	{
		$this->outputHeader($writer);
		$this->outputAllIndirectObjects($writer);
		$this->outputXref($writer);
		$this->outputTrailer($writer);
		$this->outputXrefOffset($writer);
		$this->outputFooter($writer);
	}


	public function outputHeader(Writer $writer)
	{
		$writer->writeLn("%PDF-1.2");
		$writer->writeLn("");
	}

	
	public function outputAllIndirectObjects(Writer $writer)
	{
		foreach ($this->objectManager->getAll() as $object) {
			$id = $this->objectManager->getId($object);
			$this->outputIndirectObject($writer, $id, $object);
		}
	}

	public function outputIndirectObject(Writer $writer, $id, $object)
	{
		$this->offsets[$id] = $writer->getOffset();

		$writer->writeLn("{$id} 0 obj");
		$writer->writeLn("<<");
		$writer->writeMap($object->getMap());
		$writer->writeLn(">>");
		$writer->writeLn("endobj");
		$writer->writeLn("");
	}


	public function outputXref(Writer $writer)
	{
		$this->xrefOffset = $writer->getOffset();

		$count = $this->objectManager->getCount();

		$writer->writeLn("xref");
		$writer->writeLn(sprintf("0 {$count}"));
		$writer->writeLn("0000000000 65535 f");
		foreach ($this->objectManager->getAll() as $object) {
			$id = $this->objectManager->getId($object);
			$writer->writeLn(sprintf("%010d 00000 n", $this->offsets[$id]));
		}
		$writer->writeLn("");
	}

	public function outputTrailer(Writer $writer)
	{
		$count = $this->objectManager->getCount();

		$writer->writeLn("trailer");
		$writer->writeLn("<<");
		$writer->writeMap([
			'Size' => $count, 
			'Root' => $this->catalog,
		]);
		$writer->writeLn(">>");
	}

	public function outputXrefOffset(Writer $writer)
	{
		$writer->writeLn("startxref");
		$writer->writeLn($this->xrefOffset);
	}

	public function outputFooter(Writer $writer)
	{
		$writer->writeLn("%%EOF");
	}
}
