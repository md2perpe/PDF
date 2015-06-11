<?php
class Writer
{
	protected $offset = 0;
	
	public function __construct()
	{
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
}


class Document
{
	protected $index = 1;
	protected $objects = [];
	protected $offsets = [];
	protected $xrefOffset;
	
	public function createIndirectObject($map)
	{
		$this->objects[$this->index] = $map;
		$this->index++;
	}
	
	
	public function output(Writer $writer)
	{
		$this->createIndirectObject([
			'Type' => '/Catalog',
			'Pages' => '2 0 R',
		]);
		
		$this->createIndirectObject([
			'Type' => '/Pages',
			'Kids' => '[ 3 0 R ]',
			'Count' => 1,
		]);
		
		$this->createIndirectObject([
			'Type' => '/Page',
			'Parent' => '2 0 R',
			'Resources' => '<< >>',
			'MediaBox' => '[ 0 0 1000 1000 ]',
		]);
		
		
		$this->outputHeader($writer);
		foreach ($this->objects as $id => $map) {
			$this->outputIndirectObject($writer, $id, $map);
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
	
	
	public function outputIndirectObject(Writer $writer, $id, $map)
	{
		$this->offsets[$id] = $writer->getOffset();
		
		$writer->writeLn("{$id} 0 obj");
		$writer->writeLn("<<");
		foreach ($map as $key => $value) {
			$writer->writeLn("/{$key} {$value}");
		}
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
$doc->output(new Writer());
