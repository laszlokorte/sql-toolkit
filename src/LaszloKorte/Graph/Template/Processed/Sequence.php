<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Template\Renderer;

use IteratorAggregate;
use ArrayIterator;

final class Sequence implements IteratorAggregate {

	private $children;

	public function __construct(array $children = []) {
		$this->children = $children;
	}

	public function getIterator() {
		return new ArrayIterator($this->children);
	}

	public function getPaths() {
		$result = [];
		foreach ($this->children as $c) {
			if($c instanceof ColumnValue) {
				$result[] = $c->getPath();
			}
		}

		return $result;
	}

	public function render($record, Renderer $renderer, $link = NULL) {
		return implode('', array_map(function($c) use ($link, $record, $renderer) {
			return $c->render($record, $renderer, $link);
		}, $this->children));
	}

	public function isEmpty() {
		return empty($this->children);
	}
}