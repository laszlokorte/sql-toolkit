<?php

namespace LaszloKorte\Resource\Template\Processed;

use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

final class ColumnValue {

	private $columnPath;
	private $filters;

	public function __construct(ColumnPath $columnPath, $filters) {
		$this->columnPath = $columnPath;
		$this->filters = $filter;
	}

	public function __toString() {
		return (string) $this->columnPath;
	}
}