<?php

namespace LaszloKorte\Graph\Ancestory;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\GraphDefinition;
use LaszloKorte\Graph\Entity;

use Countable;
use IteratorAggregate;
use ArrayIterator;

final class AncestorPath implements Countable {
	private $graphDef;
	private $entityIds;

	public function __construct($graphDef, $entityIds) {
		$this->graphDef = $graphDef;
		$this->entityIds = $entityIds;
	}

	public static function commonPrefix($a, $b) {
		$ids = array_filter(array_map(function($x, $y) {
			return ($x == $y) ? $x : NULL;
		}, $a->entityIds, $b->entityIds));

		return new self($this->graphDef, $ids);
	}

	public static commonPrefix2($a, $b) {
		
		$result = array_reduce(
			array_map(null, array_slice($a , 0, -1), $b), 
			function($carry, $pair) {
				if(end($carry) === NULL) {
					return $carry;
				} else {
					list($x, $y) = $pair;
					array_push($carry, $x);
					if($x !== NULL && $x != $y) {
						array_push($carry, NULL);
					}
					return $carry;
				}
			}, 
			[]
		);

		array_pop($result);

		return $result;
	}

	public function count() {
		return count($this->entityIds);
	}

	public function getIterator() {
		return new ArrayIterator(array_map(function($id) {
			return new Entity($this->graphDef, $id);
		}, $this->entityIds));
	}
}