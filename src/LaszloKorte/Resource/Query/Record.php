<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Path\OwnColumnPath;
use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Path\ForeignColumnPath;
use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Graph\Association\ParentAssociation;
use LaszloKorte\Resource\Query\Naming\Convention;
use LaszloKorte\Resource\Query\Naming\NestedConvention;

use ArrayAccess;

final class Record implements ArrayAccess {
	private $fields;
	private $namingConvention;

	public function __construct($fields, Convention $namingConvention = NULL) {
		$this->fields = $fields;
		$this->namingConvention = $namingConvention ?? new NestedConvention();
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

	public function id(Entity $e, $asArray = FALSE) {
		$table = $e->id();
		$components = array_map(function($col) use($table) {
			return $this[new OwnColumnPath($table, $col)];
		}, $e->idColumns());
		return $asArray ? $components : implode(':', $components);
	}

	public function foreignId(ParentAssociation $e) {
		$tablePath = new TablePath($e->toLink());
		return implode(':', array_map(function($col) use($tablePath) {
			return $this[new ForeignColumnPath($tablePath, $col)];
		}, $e->getTargetEntity()->idColumns()));
	}

	private function propName(ColumnPath $path) {
		return $this->namingConvention->columnName($path);
	}

	public function count($field) {
		$propName = $this->namingConvention->aggregationName('COUNT', $field->getName());

		return $this->fields->$propName;
	}
}