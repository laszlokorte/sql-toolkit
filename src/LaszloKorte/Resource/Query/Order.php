<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Presenter\Identifier;

use LaszloKorte\Presenter\Path\ColumnPath;

final class Order {
	private $column;
	private $direction;

	public function __construct(ColumnPath $column, $direction = 'ASC') {
		$this->column = $column;
		$this->direction = $direction;
	}

	public function getColumn() {
		return $this->column;
	}

	public function getDirection() {
		return $this->direction;
	}
}