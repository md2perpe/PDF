<?php
class Document
{
	public function output()
	{
		header('Content-Type: application/pdf');
		
		echo <<<EOT
%PDF-1.2

% Catalog
1 0 obj
<<
/Type /Catalog
/Pages 2 0 R
>>
endobj

% Root page tree
2 0 obj
<<
/Type /Pages
/Kids [ 3 0 R ]
/Count 1
>>
endobj

% Only page
3 0 obj
<<
/Type /Page
/Parent 2 0 R
/Resources << >>
/MediaBox [ 0 0 1000 1000 ]
>>
endobj

xref
0 4
0000000000 65535 f
0000000023 00000 n
0000000098 00000 n
0000000179 00000 n

trailer
<<
/Size 4
/Root 1 0 R 
>>
startxref
282
%%EOF
EOT;
	}
}

$doc = new Document();
$doc->output();
