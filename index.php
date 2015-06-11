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
	public function output(Writer $writer)
	{
		$this->outputHeader($writer);
		$this->outputCatalog($writer);
		$this->outputPages($writer);
		$this->outputPage($writer);
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
	
	
	public function outputIndirectObject(Writer $writer, $comment, $id, $map)
	{
		$writer->writeLn("% {$comment}");
		$writer->writeLn("{$id} 0 obj");
		$writer->writeLn("<<");
		foreach ($map as $key => $value) {
			$writer->writeLn("/{$key} {$value}");
		}
		$writer->writeLn(">>");
		$writer->writeLn("endobj");
		$writer->writeLn("");
	}
	
	
	public function outputCatalog(Writer $writer)
	{
		$this->outputIndirectObject($writer, 'Catalog', 1, [
			'Type' => '/Catalog',
			'Pages' => '2 0 R',
		]);
	}
	
	public function outputPages(Writer $writer)
	{
		$this->outputIndirectObject($writer, 'Root page tree', 2, [
			'Type' => '/Pages',
			'Kids' => '[ 3 0 R ]',
			'Count' => 1,
		]);
	}
	
	public function outputPage(Writer $writer)
	{
		$this->outputIndirectObject($writer, 'Only page', 3, [
			'Type' => '/Page',
			'Parent' => '2 0 R',
			'Resources' => '<< >>',
			'MediaBox' => '[ 0 0 1000 1000 ]',
		]);
	}

	public function outputXref(Writer $writer)
	{
		$writer->writeLn("xref");
		$writer->writeLn("0 4");
		$writer->writeLn("0000000000 65535 f");
		$writer->writeLn("0000000023 00000 n");
		$writer->writeLn("0000000098 00000 n");
		$writer->writeLn("0000000179 00000 n");
		$writer->writeLn("");
	}
	
	public function outputTrailer(Writer $writer)
	{
		$writer->writeLn("trailer");
		$writer->writeLn("<<");
		$writer->writeLn("/Size 4");
		$writer->writeLn("/Root 1 0 R");
		$writer->writeLn(">>");
	}
	
	public function outputXrefOffset(Writer $writer)
	{
		$writer->writeLn("startxref");
		$writer->writeLn("282");
	}
	
	public function outputFooter(Writer $writer)
	{
		$writer->writeLn("%%EOF");
	}
}

$doc = new Document();
$doc->output(new Writer());
