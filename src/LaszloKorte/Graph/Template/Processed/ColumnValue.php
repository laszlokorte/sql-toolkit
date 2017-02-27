<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Template\Renderer;

use LaszloKorte\Graph\Path\ColumnPath;
use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

final class ColumnValue {

	private $columnPath;
	private $filters;

	public function __construct(ColumnPath $columnPath, $filters) {
		$this->columnPath = $columnPath;
		$this->filters = $filters;
	}

	public function __toString() {
		return (string) $this->columnPath;
	}

	public function getPath() {
		return $this->columnPath;
	}

	public function relativeTo(TablePath $base) {
		return new self($this->columnPath->relativeTo($base), $this->filters);
	}

	public function render($record, Renderer $renderer, $link = NULL) {
		return array_reduce($this->filters, function($val, $f) use ($renderer) {
			return $f->apply($val, $renderer);
		}, $renderer->unsafeText($record[$link ? $this->columnPath->relativeTo(new TablePath($link)) : $this->columnPath]));
	}
}