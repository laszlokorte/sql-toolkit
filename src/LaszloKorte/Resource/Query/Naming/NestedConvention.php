<?php

namespace LaszloKorte\Resource\Query\Naming;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Path\ForeignColumnPath;
use LaszloKorte\Graph\Path\OwnColumnPath;

final class NestedConvention implements Convention {
	public function aggregationName($type, Identifier $name) {
		return sprintf(
			'aggr_%s_%s', 
			$name, 
			$type
		);
	}

	public function columnName(ColumnPath $path) {
		if($path instanceof ForeignColumnPath) {
			return sprintf(
				'foreign_%s_%s', 
				implode('_', array_map(
					[$this,'pathName'], 
					$path->getTablePath()->getLinks()
				)
			), $path->getColumnName());
		} elseif ($path instanceof OwnColumnPath) {
			return sprintf(
				'own_%s_%s', 
				$path->getTableName(), 
				$path->getColumnName()
			);
		} else {
			throw new \Exception(sprintf("Unexptected column path: %s", get_class($path)));
		}
	}

	private function pathName($p) {
		return $p->getName();
	}
}