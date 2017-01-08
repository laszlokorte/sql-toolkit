<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\ColumnAnnotation as CA;
use LaszloKorte\Schema\Column;

final class FieldBuilder {
	private $prevAnnotations = [];
	private $columns;

	public function __construct(Column $column) {
		$this->column = $column;
	}

	public function requireUnique(CA\Annotation $a) {
		$class = get_class($a);
		if(in_array($class, $this->prevAnnotations)) {
			throw new \Exception(sprintf('Duplicate annotation "%s"', $class));
		}

		$this->prevAnnotations []= $class;
	}

	public function setAggregatable($isAggregatable) {

	}

	public function setDescription($description) {

	}

	public function setCollectionVisible($visible) {

	}

	public function setType($type, $params) {

	}

	public function setLinked($isLinked) {

	}

	public function setSecret($isSecret) {

	}

	public function setTitle($title) {

	}

	public function setVisible($isVisible) {

	}

}