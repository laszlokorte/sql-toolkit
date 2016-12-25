<?php

namespace LaszloKorte\Mapper\Collection;

use Iterator as SPLIterator;

class Iterator implements SPLIterator {
	private $collection;
	private $currentIndex;

	public function __construct(FetchedCollection $collection) {
		$this->collection = $collection;
		$this->currentIndex = 0;
	}

	public function rewind() {
		$this->currentIndex = 0;
	}

	public function current() {
		return $this->collection->get($this->currentIndex);
	}

	public function key() {
		return $this->currentIndex;
	}

	public function next() {
		++$this->currentIndex;
	}

	public function valid() {
		return $this->currentIndex < count($this->collection);
	}

	public function __destruct() {
	}
}
