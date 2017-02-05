<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Presenter\Identifier;

use LaszloKorte\Presenter\Path\ColumnPath;

final class EntityQuery {

	private $tableName;
	private $columns = null;
	private $coundChildren = false;
	private $offset = 0;
	private $limit = null;
	private $orders = null;

	public function __construct(Identifier $tableName) {
		$this->tableName = $tableName;
	}

	public function includeColumn(ColumnPath $col) {
		if ($this->columns === NULL) {
			$this->columns = [];
		}

		$this->columns[] = $col;

		$this->columns = array_unique($this->columns);
	}

	public function countChildren() {
		$this->coundChildren = true;
	}

	public function limit($num) {
		$this->limit = $num;
	}

	public function offset($num) {
		$this->offset = $num;
	}

	public function orderBy(...$orders) {
		foreach($order AS $o) {
			if(!$o instanceof Order) {
				throw new \Exception(sprintf("Expect order to be instanceof %s but was %s", Order::class, get_class($o)));
			}
		}
		$this->orders = $orders;
	}

	public function getPrepared() {

	}

	public function __toString() {
		return sprintf('SELECT %s FROM %s', $this->columns === NULL ? '*' : implode(', ', $this->columns), $this->tableName);
	}

}