<?php

namespace LaszloKorte\Resource\Query\Naming;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Path\ForeignColumnPath;
use LaszloKorte\Graph\Path\OwnColumnPath;

final class FlatConvention implements Convention {
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
				'%s_%s', 
				$path->getTablePath()->getTarget(), 
				$path->getColumnName()
			);
		} elseif ($path instanceof OwnColumnPath) {
			return sprintf(
				'%s_%s', 
				$path->getTableName(), 
				$path->getColumnName()
			);
		} else {
			throw new \Exception(sprintf("Unexptected column path: %s", get_class($path)));
		}
	}
}