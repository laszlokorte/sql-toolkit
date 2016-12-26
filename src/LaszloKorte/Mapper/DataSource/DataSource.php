<?php

namespace LaszloKorte\Mapper\DataSource;

use LaszloKorte\Mapper\Query\Query;

use PDO;

final class DataSource {
	private $connection;

	public function __construct(PDO $connection) {
		$this->connection = $connection;
	}

	public function resultFor(Query $query) {
		$stmt = $this->statementFor($query);

		return new ResultSet($stmt);
	}

	private function statementFor(Query $query) {
		$fields = $this->fieldsFor($query);
		$tables = $this->tablesFor($query);
		$condition = $this->conditionsFor($query);
		$order = $this->orderFor($query);
		$limit = $this->limitFor($query);
		$offset = $this->offsetFor($query);

		$queryString = sprintf('
			SELECT %s 
			FROM %s 
			WHERE %s 
			ORDER BY %s 
			LIMIT %s
			OFFSET %d',
			$fields->sqlString,
			$tables->sqlString,
			$condition->sqlString,
			$order->sqlString,
			$limit->sqlString,
			$offset->sqlString
		);

		$queryString = 'SELECT id, date, label FROM day WHERE 1 ORDER BY 1 LIMIT 1000 OFFSET 0';
		$stmt = $this->connection->prepare($queryString);

		$bindings = array_merge(
			$fields->bindings,
			$tables->bindings,
			$condition->bindings,
			$order->bindings,
			$limit->bindings,
			$offset->bindings
		);

		foreach($bindings AS $placeholder => $val) {
			$stmt->bind($placeholder, $val);
		}

		return $stmt;
	}

	private function fieldsFor(Query $query, $depth = 0) {
		$sql = implode(', ', array_map(function($f) {
			return $f;
		}, []));
		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function tablesFor(Query $query, $depth = 0) {
		$sql = 'tableName'; // JOIN ... ON ...
		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function conditionsFor(Query $query, $depth = 0) {
		$sql = '1';

		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function orderFor(Query $query, $depth = 0) {
		$sql = '0';

		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function limitFor(Query $query, $depth = 0) {
		$sql = '1000';

		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function offsetFor(Query $query, $depth = 0) {
		$sql = '0';

		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

}
