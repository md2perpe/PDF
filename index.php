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
		$writer->write("%PDF-1.2\r\n");
		$writer->write("\r\n");
	}
	
	
	public function outputIndirectObject(Writer $writer, $comment, $id, $map)
	{
		$writer->write("% {$comment}\r\n");
		$writer->write("{$id} 0 obj\r\n");
		$writer->write("<<\r\n");
		foreach ($map as $key => $value) {
			$writer->write("/{$key} {$value}\r\n");
		}
		$writer->write(">>\r\n");
		$writer->write("endobj\r\n");
		$writer->write("\r\n");
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
		$writer->write("xref\r\n");
		$writer->write("0 4\r\n");
		$writer->write("0000000000 65535 f\r\n");
		$writer->write("0000000023 00000 n\r\n");
		$writer->write("0000000098 00000 n\r\n");
		$writer->write("0000000179 00000 n\r\n");
		$writer->write("\r\n");
	}
	
	public function outputTrailer(Writer $writer)
	{
		$writer->write("trailer\r\n");
		$writer->write("<<\r\n");
		$writer->write("/Size 4\r\n");
		$writer->write("/Root 1 0 R\r\n");
		$writer->write(">>\r\n");
	}
	
	public function outputXrefOffset(Writer $writer)
	{
		$writer->write("startxref\r\n");
		$writer->write("282\r\n");
	}
	
	public function outputFooter(Writer $writer)
	{
		$writer->write("%%EOF\r\n");
	}
}

$doc = new Document();
$doc->output(new Writer());
