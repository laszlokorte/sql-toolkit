<?php

namespace LaszloKorte\Graph\Ancestory;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphDefinition;
use LaszloKorte\Graph\Entity;

use Countable;
use Iterator;
use ArrayIterator;

final class AncestorIterator implements Iterator {
	private $graphDef;
	private $entityId;
	private $currentEntityId;
	private $index;

	public function __construct($graphDef, $entityId) {
		$this->graphDef = $graphDef;
		$this->entityId = $entityId;
	}

	public function current() {
		return new Entity($this->graphDef, $this->entityId);
	}

	public function key() {
		return $this->index;
	}

	public function next() {
		$entityDef = $this->graphDef->getEntity($this->currentEntityId);

		$this->currentEntityId = $entityDef->getParentId();
		$this->index += 1;
	}

	public function rewind() {
		$this->index = 0;
		$this->currentEntityId = $this->entityId;
	}

	public function valid() {
		return $this->currentEntityId !== NULL;
	}

}