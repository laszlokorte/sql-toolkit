<?php

namespace LaszloKorte\Mapper\Record;

use LaszloKorte\Mapper\Collection\Collection;

final class Record {
	private $key;
	private $fields;
	private $collection;

	public function __construct(Key $key, $fields = [], Collection $collection = null) {
		$this->identifier = $identifier;
		$this->collection = $collection;
	}

	public function __get($key) {

	}

	public function val($key) {

	}

	public function children($key) {

	}

	public function parent($key) {

	}
}
