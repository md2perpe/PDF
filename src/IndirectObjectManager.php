<?php
class IndirectObjectManager
{
	protected $objects = [];
	protected $hashMap = [];
	protected $lastId = 0;

	public function __construct()
	{
	}

	public function register(IndirectObject $object)
	{
		$hash = spl_object_hash($object);

		if (!isset($this->hashMap[$hash])) {
			$this->objects[] = $object;
			$this->hashMap[$hash] = ++ $this->lastId;
		}

		return $this;
	}

	public function getId(IndirectObject $object)
	{
		$hash = spl_object_hash($object);

		if (isset($this->hashMap[$hash])) {
			return $this->hashMap[$hash];
		}
		else {
			throw new \Exception("Object with hash '{$hash}' not registered");
		}
	}

	public function getAll()
	{
		return $this->objects;
	}

	public function getCount()
	{
		return $this->lastId;
	}
}
