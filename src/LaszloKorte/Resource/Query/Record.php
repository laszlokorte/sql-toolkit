<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Path\OwnColumnPath;
use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Path\ForeignColumnPath;
use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Graph\Association\ParentAssociation;

use ArrayAccess;

final class Record implements ArrayAccess {
	private $fields;

	public function __construct($fields) {
		$this->fields = $fields;
	}

	public function offsetGet($offset) {
		if ($offset instanceof ColumnPath) {
			$propName = $this->propName($offset);
			return $this->fields->$propName;
		} else {
			echo get_class($offset);
		}
	}

	public function offsetSet($offset, $val) {

	}

	public function offsetExists($offset) {
		if ($offset instanceof OwnColumnPath || $offset instanceof ForeignColumnPath) {
			$propName = $this->propName($offset);

			return property_exists($this->fields, $propName);
		} else {
			return false;
		}
	}

	public function offsetUnset($offset) {
		
	}

	public function id(Entity $e) {
		$table = $e->id();
		return implode(':', array_map(function($col) use($table) {
			return $this[new OwnColumnPath($table, $col)];
		}, $e->idColumns()));
	}

	public function foreignId(ParentAssociation $e) {
		$tablePath = new TablePath($e->toLink());
		return implode(':', array_map(function($col) use( $tablePath) {
			return $this[new ForeignColumnPath($tablePath, $col)];
		}, $e->getTargetEntity()->idColumns()));
	}

	private function propName(ColumnPath $offset) {
		if($offset instanceof ForeignColumnPath) {
			return sprintf('foreign_%s_%s', implode('_', array_map(function($l) {
				return $l->getName();
			}, $offset->getTablePath()->getLinks())), $offset->getColumnName());
		} elseif ($offset instanceof OwnColumnPath) {
			return sprintf('own_%s_%s', $offset->getTableName(), $offset->getColumnName());
		}
	}

	public function count($field) {
		$propName = sprintf('aggr_%s_COUNT', $field->getName());

		return $this->fields->$propName;
	}
}