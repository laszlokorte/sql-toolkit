<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Path\OwnColumnPath;

use ArrayAccess;

final class Record implements ArrayAccess {
	public function offsetGet($offset) {
		if ($offset instanceof OwnColumnPath) {
			$propName = $this->propName($offset);
			return $this->$propName;
		} else {
			echo get_class($offset);
		}
	}

	public function offsetSet($offset, $val) {

	}

	public function offsetExists($offset) {
		if ($offset instanceof OwnColumnPath) {
			$propName = $this->propName($offset);

			return property_exists($this, $propName);
		} else {
			return false;
		}
	}

	public function offsetUnset($offset) {
		
	}

	private function propName(OwnColumnPath $offset) {
		return sprintf('own_%s_%s', $offset->getTableName(), $offset->getColumnName());
	}

	public function count($field) {
		$propName = sprintf('aggr_%s_COUNT', $field->getName());

		return $this->$propName;
	}
}