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
	private $keyColumns = null;
	private $scope = null;
	private $grouping = null;
	private $flatNames = false;

	public function __construct(Identifier $tableName) {
		$this->tableName = $tableName;
	}

	public function setKeyColumns($cols) {
		$this->keyColumns = $cols;
	}

	public function setScope(Scope $scope) {
		$this->scope = $scope;
	}

	public function setGrouping(Grouping $grouping) {
		$this->grouping = $grouping;
	}

	public function isGrouped() {
		return $this->grouping !== NULL;
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

	public function flatNames() {
		$this->flatNames = TRUE;
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

		if($this->scope) {
			$scopeJoin = implode(' ', $this->joinsForScope($this->scope));
			$tables .= $scopeJoin;
		}

		if($this->grouping) {
			$groupingJoin = implode(' ', $this->joinsForGrouping($this->grouping));
			$tables .= $groupingJoin;
		}

		if(empty($columns)) {
			$columns = [sprintf('%s.*', $this->tableName)];
		} else {
			$columns = array_map([$this, 'columnProjection'], $columns);
		}

		if(!empty($this->aggregations)) {
			foreach($this->aggregations AS $aggr) {
				$conditions = $this->joinCondition($this->tableName, sprintf('aggr_%s', $aggr->getName()), $aggr->getLink());
				$subQuery = sprintf("SELECT\n\t\tCOUNT(*)\n\tFROM \n\t\t%s AS aggr_%s\n\tWHERE\n\t\t%s", $aggr->getLink()->getTarget(), $aggr->getName(), implode(' AND ', $conditions));

				$columns []= sprintf("(\n\t%s\n\t) AS aggr_%s_%s", $subQuery, $aggr->getName(), $aggr->getType());
			}
		}

		$ordering = !empty($this->orders) ?  
			array_map(function($o) {
				$c = $o->getColumnOrAggregation();
				if($c instanceof OwnColumnPath) {
					$colName = $this->ownColumnAlias($c);
				} elseif($c instanceof ForeignColumnPath) {
					$colName = $this->foreignColumnAlias($c);
				} elseif($c instanceof Aggregation) {
					$colName = sprintf("aggr_%s_%s", $c->getName(), $c->getType());
				}
				return sprintf('%s %s', $colName, $o->getDirection());
			}, $this->orders)
		: [];

		$where = $this->keyColumns ? array_map(function($col, $i) {
			return sprintf('%s.%s = :%s', $this->tableName, $col->getColumnName(), $i);
		}, $this->keyColumns, array_keys($this->keyColumns)) : [];

		if($this->scope) {
			$scopeCols = $this->scope->getColumnNames();
			$scopeTable = $this->scope->getTargetTable();
			array_push($where, ...array_map(function($colName) use($scopeTable) {
				return sprintf('scope_%s.%s = :scope_%s', $scopeTable, $colName, $colName);
			}, $scopeCols));
		}

		if($this->grouping) {
			$groupingTable = $this->grouping->getTargetTable();
			$columns[] = sprintf(
				'IFNULL(CONCAT(\'g_\', %s), \'none\') AS grouping', 
				implode(', ', array_map(function($col) use ($groupingTable) {
					return sprintf('grouping_%s.%s', $groupingTable, $col);
				}, $this->grouping->getTargetColumns()))
			);
			array_unshift($ordering, 'grouping ASC');
		}

		return sprintf("SELECT\n\t%s\nFROM\n\t%s\nWHERE\n\t%s\nORDER BY\n\t%s%s", 
			implode(",\n\t", $columns),
			$tables, 
			implode(' AND ', array_pad($where, 1, '1')), 
			implode(",\n\t", array_pad($ordering, 1, '1')), 
			is_null($this->limit) ? '' : 
				sprintf("\nLIMIT %d\nOFFSET %d", $this->limit, $this->offset)
			);
	}

	private function ownColumnAlias($column) {
		return $this->flatNames ? 
		sprintf('%s_%s', $this->tableName, $column->getColumnName())
		:
		sprintf('own_%s_%s', $this->tableName, $column->getColumnName());
	}

	private function foreignColumnAlias($column) {
		return $this->flatNames ? 
		sprintf('%s_%s', $column->getTablePath()->getTarget(), $column->getColumnName()) 
		:
		sprintf('foreign_%s_%s', 
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
			return $this->joinsForPath($this->tableName, $p);
		}, $foreignPaths));

		return array_map(function($alias, $join) {
			return sprintf("\nLEFT JOIN %s %s\n\tON %s", $join->table, $alias, implode("\n\tAND\n\t", $join->conditions));
		}, array_keys($joins), array_values($joins));
	}

	private function joinsForPath(Identifier $tableName, TablePath $path) {
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

	private function joinsForScope(Scope $scope) {
		return array_map(function($link, $first) {
			return sprintf(
				"\nINNER JOIN %s scope_%s\n\tON %s", 
				$link->getTarget(), 
				$link->getTarget(), 
				implode("\n\tAND\n\t", 
					$this->joinCondition(
						$first ? $link->getSource() : sprintf('scope_%s', $link->getSource()), 
						sprintf('scope_%s', $link->getTarget()), 
						$link
					)
				)
			);
		}, $this->scope->getLinks(), [true]);
	}

	private function joinsForGrouping(Grouping $grouping) {
		return array_map(function($link, $first) {
			return sprintf(
				"\nLEFT JOIN %s grouping_%s\n\tON %s", 
				$link->getTarget(), 
				$link->getTarget(), 
				implode("\n\tAND\n\t", 
					$this->joinCondition(
						$first ? $link->getSource() : sprintf('grouping_%s', $link->getSource()), 
						sprintf('grouping_%s', $link->getTarget()), 
						$link
					)
				)
			);
		}, $this->grouping->getLinks(), [true]);
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