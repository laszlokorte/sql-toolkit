<?php

namespace LaszloKorte\Resource\Query\Naming;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\ColumnPath;

interface Convention {
	public function aggregationName($type, Identifier $name);

	public function columnName(ColumnPath $path);
}