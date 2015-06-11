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
	
	
	public function outputCatalog()
	{
		echo "% Catalog\r\n";
		echo "1 0 obj\r\n";
		echo "<<\r\n";
		echo "/Type /Catalog\r\n";
		echo "/Pages 2 0 R\r\n";
		echo ">>\r\n";
		echo "endobj\r\n";
		echo "\r\n";
	}
	
	public function outputPages()
	{
		echo "% Root page tree\r\n";
		echo "2 0 obj\r\n";
		echo "<<\r\n";
		echo "/Type /Pages\r\n";
		echo "/Kids [ 3 0 R ]\r\n";
		echo "/Count 1\r\n";
		echo ">>\r\n";
		echo "endobj\r\n";
		echo "\r\n";
	}
	
	public function outputPage()
	{
		echo "% Only page\r\n";
		echo "3 0 obj\r\n";
		echo "<<\r\n";
		echo "/Type /Page\r\n";
		echo "/Parent 2 0 R\r\n";
		echo "/Resources << >>\r\n";
		echo "/MediaBox [ 0 0 1000 1000 ]\r\n";
		echo ">>\r\n";
		echo "endobj\r\n";
		echo "\r\n";
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
