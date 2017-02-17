<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Identifier;

use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Path\ForeignColumnPath;
use LaszloKorte\Graph\Path\OwnColumnPath;
use LaszloKorte\Graph\Path\TablePath;

final class EntityQuery {

	private $tableName;
	private $columns = null;
	private $aggregations = null;
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

		$this->columns = array_unique($this->columns, SORT_REGULAR);
	}

	public function includeAggregation(Aggregation $aggre) {
		if($this->aggregations === NULL) {
			$this->aggregations = [];
		}

		$this->aggregations[] = $aggre;

		$this->aggregations = array_unique($this->aggregations, SORT_REGULAR);
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
		foreach($orders AS $o) {
			if(!$o instanceof Order) {
				throw new \Exception(sprintf("Expect order to be instanceof %s but was %s", Order::class, get_class($o)));
			}
		}
		$this->orders = $orders;
	}

	public function getPrepared($connection) {
		$stmt = $connection->prepare($this->build());

		return $stmt;
	}

	public function __toString() {
		return $this->build();
	}

	private function build() {
		$columns = $this->columns ?? [];
		
		usort($columns, 
			function($a, $b) {
				$diff = $a->length() - $b->length();
				return ($diff > 0) - ($diff < 0);
			}
		);

		$joins = array_unique(
			array_map(function($colPath) {
				return $colPath->getTablePath();
			}, array_filter($columns, function($colPath) {
				return $colPath instanceof ForeignColumnPath;
			})),
			SORT_REGULAR
		);

		if(!empty($joins)) {		
			$tables = sprintf('%s %s', $this->tableName, implode(' ', $this->joinsForPaths($joins)));
		} else {
			$tables = $this->tableName;
		}

		if(empty($columns)) {
			$columns = sprintf('%s.*', $this->tableName);
		} else {
			$columns = implode(",\n\t", array_map([$this, 'columnProjection'], $columns));
		}

		if(!empty($this->aggregations)) {
			foreach($this->aggregations AS $aggr) {
				$conditions = $this->joinCondition($this->tableName, sprintf('aggr_%s', $aggr->getName()), $aggr->getLink());
				$subQuery = sprintf("SELECT\n\t\tCOUNT(*)\n\tFROM \n\t\t%s AS aggr_%s\n\tWHERE\n\t\t%s", $aggr->getLink()->getTarget(), $aggr->getName(), implode(' AND ', $conditions));

				$columns .= sprintf(",\n\t(\n\t%s\n\t) AS aggr_%s_%s", $subQuery, $aggr->getName(), $aggr->getType());
			}
		}

		$ordering = !empty($this->orders) ? implode(",\n\t", 
			array_map(function($o) {
				$c = $o->getColumnOrAggregation();
				if($c instanceof OwnColumnPath) {
					$colName = sprintf('own_%s_%s', $this->tableName, $c->getColumnName());
				} elseif($c instanceof ForeignColumnPath) {
					$colName = sprintf('foreign_%s_%s', implode('_', array_map(function($l) {
						return $l->getName();
					}, $c->getTablePath()->getLinks())), $c->getColumnName());
				} elseif($c instanceof Aggregation) {
					$colName = sprintf("aggr_%s_%s", $c->getName(), $c->getType());
				}
				return sprintf('%s %s', $colName, $o->getDirection());
			}, $this->orders)
		) : '1';

		return sprintf("SELECT\n\t%s\nFROM\n\t%s\nORDER BY\n\t%s%s", $columns, $tables, $ordering, is_null($this->limit) ? '' : sprintf("\nLIMIT %d\nOFFSET %d", $this->limit, $this->offset));
	}

	private function ownColumnAlias($column) {
		return sprintf('own_%s_%s', $this->tableName, $column->getColumnName());
	}

	private function foreignColumnAlias($column) {
		return sprintf('foreign_%s_%s', 
			implode('_', 
				array_map(function($l) {
					return $l->getName();
				}, $column->getTablePath()->getLinks())
			), 
			$column->getColumnName()
		);
	}

	private function columnProjection($column) {
		if($column instanceof OwnColumnPath) {
			return sprintf('%s.%s AS %s', $this->tableName, $column->getColumnName(), $this->ownColumnAlias($column));
		} else {
			return sprintf('%s_%s.%s AS %s', 
				$this->tableName, 
				implode('_', 
					array_map(function($l) {
						return $l->getName();
					}, $column->getTablePath()->getLinks())
				), $column->getColumnName(),
				$this->foreignColumnAlias($column)
			);
		}
	}

	private function joinsForPaths(array $foreignPaths) {
		$joins = array_merge(...array_map(function($p) {
			return $this->joinForPath($this->tableName, $p);
		}, $foreignPaths));

		return array_map(function($alias, $join) {
			return sprintf("\nLEFT JOIN %s %s\n\tON %s", $join->table, $alias, implode("\n\tAND\n\t", $join->conditions));
		}, array_keys($joins), array_values($joins));
	}

	private function joinForPath(Identifier $tableName, TablePath $path) {
		$joins = [];
		$prevAlias = $tableName;
		foreach($path->getLinks() AS $link) {
			$alias = sprintf('%s_%s', $prevAlias, $link->getName());
			$target = $link->getTarget();

			$joins[$alias] = (object)[
				'table' => $target,
				'conditions' => $this->joinCondition($prevAlias, $alias, $link),
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
		}, $relationship->getSourceColumns(), $relationship->getTargetColumns());
	}

}