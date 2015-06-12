<?php
class Writer
{
	protected $objectManager;

	protected $offset = 0;

	public function __construct(IndirectObjectManager $objectManager)
	{
		$this->objectManager = $objectManager;

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
		$id = $this->objectManager->getId($object);
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
