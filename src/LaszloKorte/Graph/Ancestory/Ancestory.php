<?php

namespace LaszloKorte\Graph\Ancestory;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphDefinition;
use LaszloKorte\Graph\Entity;

use Countable;
use IteratorAggregate;

final class Ancestory implements IteratorAggregate, Countable {
	private $graphDef;
	private $entityId;

	public function __construct($graphDef, $entityId) {
		$this->graphDef = $graphDef;
		$this->entityId = $entityId;
	}

	public function getIterator() {
		return new AncestorIterator($this->graphDef, $this->entityId);
	}

	public function count() {
		$result = 0;

		foreach ($this as $e) {
			$result += 1;
		}

		return $result;
	}

	public function toPath() {
		$ids = array_reverse(array_map(function($entity) {
			return $entity->id();
		}, iterator_to_array($this)));
		
		return new AncestorPath($this->graphDef, $ids);
	}
}