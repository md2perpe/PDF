<?php
class Document
{
	public function output()
	{
		header('Content-Type: application/pdf');
		
		$this->outputHeader();
		$this->outputCatalog();
		$this->outputPages();
		$this->outputPage();
		$this->outputXref();
		$this->outputTrailer();
		$this->outputXrefOffset();
		$this->outputFooter();
	}
	
	
	public function outputHeader()
	{
		echo "%PDF-1.2\r\n";
		echo "\r\n";
	}
	
	
	public function outputIndirectObject($comment, $id, $map)
	{
		echo "% {$comment}\r\n";
		echo "{$id} 0 obj\r\n";
		echo "<<\r\n";
		foreach ($map as $key => $value) {
			echo "/{$key} {$value}\r\n";
		}
		echo ">>\r\n";
		echo "endobj\r\n";
		echo "\r\n";
	}
	
	
	public function outputCatalog()
	{
		$this->outputIndirectObject('Catalog', 1, [
			'Type' => '/Catalog',
			'Pages' => '2 0 R',
		]);
	}
	
	public function outputPages()
	{
		$this->outputIndirectObject('Root page tree', 2, [
			'Type' => '/Pages',
			'Kids' => '[ 3 0 R ]',
			'Count' => 1,
		]);
	}
	
	public function outputPage()
	{
		$this->outputIndirectObject('Only page', 3, [
			'Type' => '/Page',
			'Parent' => '2 0 R',
			'Resources' => '<< >>',
			'MediaBox' => '[ 0 0 1000 1000 ]',
		]);
	}

	public function outputXref()
	{
		echo "xref\r\n";
		echo "0 4\r\n";
		echo "0000000000 65535 f\r\n";
		echo "0000000023 00000 n\r\n";
		echo "0000000098 00000 n\r\n";
		echo "0000000179 00000 n\r\n";
		echo "\r\n";
	}
	
	public function outputTrailer()
	{
		echo "trailer\r\n";
		echo "<<\r\n";
		echo "/Size 4\r\n";
		echo "/Root 1 0 R\r\n";
		echo ">>\r\n";
	}
	
	public function outputXrefOffset()
	{
		echo "startxref\r\n";
		echo "282\r\n";
	}
	
	public function outputFooter()
	{
		echo "%%EOF\r\n";
	}
}

$doc = new Document();
$doc->output();
