<?php
class Writer
{
	protected $document;
	
	protected $offset = 0;

	public function __construct(Document $document)
	{
		$this->document = $document;
		
		header('Content-Type: application/pdf');
	}

	public function getOffset()
	{
		return $this->offset;
	}

	public function write($s)
	{
		echo $s;
		$this->offset += strlen($s);
	}

	public function writeLn($s)
	{
		$this->write("$s\r\n");
	}
	
	public function writeIndirectObject(IndirectObject $object)
	{
		$id = $this->document->getIndex($object);
		$this->write("{$id} 0 R");
	}

	public function writeMap(array $map)
	{
		foreach ($map as $key => $value) {
			$this->write("/{$key} ");
			
			if ($value instanceof IndirectObject) {
				$this->writeIndirectObject($value);
			}
			elseif (is_array($value)) {
				$this->write("[ ");
				foreach ($value as $object) {
					$this->writeIndirectObject($object);
				}
				$this->write(" ]");
			}
			else {
				$this->write($value);
			}
			
			$this->writeLn("");
		}
	}
}


abstract class IndirectObject
{
	abstract public function getMap();
}

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


class Document
{
	protected $index = 1;
	protected $objects = [];
	protected $offsets = [];
	protected $xrefOffset;

	protected $catalog;
	protected $currentPages;
	protected $currentPage;

	public function __construct()
	{
		$catalog = new Catalog();
		$this->registerIndirectObject($catalog);

		$pages = new Pages();
		$this->registerIndirectObject($pages);

		$page = new Page($pages);
		$this->registerIndirectObject($page);

		$catalog->setPages($pages);
		$pages->addPage($page);

		$this->catalog = $catalog;
		$this->currentPages = $pages;
		$this->currentPage = $page;
	}
	
	
	public function getIndex(IndirectObject $object)
	{
		foreach ($this->objects as $id => $obj) {
			if ($obj === $object) {
				return $id;
			}
		}
		
		throw new \Exception('Indirect object not registered');
	}


	public function registerIndirectObject($object)
	{
		$this->objects[$this->index] = $object;
		$this->index++;
	}



	public function output(Writer $writer)
	{
		$this->outputHeader($writer);
		foreach ($this->objects as $id => $object) {
			$this->outputIndirectObject($writer, $id, $object);
		}

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

		$count = count($this->objects);

		$writer->writeLn("xref");
		$writer->writeLn(sprintf("0 {$count}"));
		$writer->writeLn("0000000000 65535 f");
		foreach ($this->objects as $id => $object) {
			$writer->writeLn(sprintf("%010d 00000 n", $this->offsets[$id]));
		}
		$writer->writeLn("");
	}

	public function outputTrailer(Writer $writer)
	{
		$count = count($this->objects);

		$writer->writeLn("trailer");
		$writer->writeLn("<<");
		$writer->writeLn("/Size {$count}");
		$writer->writeLn("/Root 1 0 R");
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

$doc = new Document();
$doc->output(new Writer($doc));
