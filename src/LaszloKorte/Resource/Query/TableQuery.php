<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Schema\Table;

final class TableQuery {

	private $table;
	private $columns = null;
	private $coundChildren = false;
	private $offset = 0;
	private $limit = null;

	public function __construct(Table $table) {
		$this->table = $table;
	}

	public function countChildren() {

	}

	public function limit($num) {

	}

	public function offset($num) {

	}

	public function orderBy(...$orders) {

	}

	public function getPrepared() {

	}

}