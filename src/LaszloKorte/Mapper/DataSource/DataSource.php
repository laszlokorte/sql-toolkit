<?php

namespace LaszloKorte\Mapper\DataSource;

use LaszloKorte\Mapper\Query\Query;
use LaszloKorte\Mapper\Path\ForeignPath;
use LaszloKorte\Mapper\Type;

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

		var_dump($queryString);

		// $queryString = 'SELECT id, date, label FROM day WHERE 1 ORDER BY 1 LIMIT 1000 OFFSET 0';
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
		$type = $query->getType();
		$sql = implode(', ', array_map(function($f) use ($type) {
			return sprintf('%s.%s', $type->getName(), $f->getName());
		}, $type->fields()));

		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function tablesFor(Query $query, $depth = 0) {
		$sourceType = $query->getType();
		$tableName = (string)$sourceType->getTableName();
		$sources = [$tableName];

		$paths = $query->getPaths();
		$foreignPaths = array_unique(
			array_filter($paths, function($p) {
					return ($p instanceof ForeignPath) && $p->isParentPath();
			}),
			SORT_REGULAR
		);
		usort($foreignPaths, 
			function($a, $b) {
				$diff = $a->length() - $b->length();
				return ($diff > 0) - ($diff < 0);
			}
		);

		$joins = $this->joinsForPaths($sourceType, $foreignPaths);

		$sql = count($joins) > 0 ? sprintf('%s %s', $tableName, implode(' ', $joins)) : $tableName; // JOIN ... ON ...
		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function joinsForPaths(Type $startType, array $foreignPaths) {
		$joins = array_merge(...array_map(function($p) use($startType) {
			return $this->joinForPath($startType, $p);
		}, $foreignPaths));

		return array_map(function($alias, $join) {
			return sprintf('INNER JOIN %s %s ON %s', $join->table, $alias, implode(' AND ', $join->conditions));
		}, array_keys($joins), array_values($joins));
	}

	private function joinForPath(Type $startType, ForeignPath $path) {
		$joins = [];
		$prevAlias = (string)$startType->getName();
		foreach($path->getRelationships() AS $rel) {
			$alias = sprintf('%s_%s', $prevAlias, $rel->getName());
			$target = $rel->getTargetType();

			$joins[$alias] = (object)[
				'table' => $target->getName(),
				'conditions' => $this->joinCondition($prevAlias, $alias, $rel),
			];

			$prevAlias = $alias;
		}

		return $joins;
	}

	private function joinCondition($sourceAlias, $targetAlias, $relationship) {
		return array_map(function($sourceField, $targetField) use($sourceAlias, $targetAlias) {
			return sprintf('%s.%s = %s.%s', 
				$sourceAlias, $sourceField,
				$targetAlias, $targetField
			);
		}, $relationship->getSourceKeys(), $relationship->getTargetKeys());
	}

	private function conditionsFor(Query $query, $depth = 0) {
		$sql = '1';

		return (object) [
			'sqlString' => $sql,
			'bindings' => [],
		];
	}

	private function orderFor(Query $query, $depth = 0) {
		$sql = '1';

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
