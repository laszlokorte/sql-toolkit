<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\ColumnAnnotation as CA;
use LaszloKorte\Schema\Column;

final class FieldBuilder {
	private $prevAnnotations = [];
	private $column;

	private $isAggregatable = false;
	private $description = null;
	private $visibleInCollection = true;
	private $typeName = null;
	private $typeParams = null;
	private $isLinked = null;
	private $isSecret = false;
	private $title = null;
	private $isVisible = true;

	private $unknownAnnotations = [];

	public function __construct(Column $column) {
		$this->column = $column;
	}

	public function reportUnknownAnnotation($annotation) {
		$this->unknownAnnotations[] = $annotation;
	}

	public function requireUnique(CA\Annotation $a) {
		$class = get_class($a);
		if(in_array($class, $this->prevAnnotations)) {
			throw new \Exception(sprintf('Duplicate annotation "%s"', $class));
		}

		$this->prevAnnotations []= $class;
	}

	public function setAggregatable($isAggregatable) {
		if(!is_bool($isAggregatable)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isAggregatable = $isAggregatable;
	}

	public function setDescription($description) {
		if(!is_string($description)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->description = $description;
	}

	public function setCollectionVisible($visible) {
		if(!is_bool($visible)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->visibleInCollection = $visible;
	}

	public function setType($type, array $params) {
		if(!is_string($type)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->type = $type;
		$this->typeParams = $params;
	}

	public function setLinked($isLinked) {
		if(!is_bool($isLinked)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isLinked = $isLinked;
	}

	public function setSecret($isSecret) {
		if(!is_bool($isSecret)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isSecret = $isSecret;
	}

	public function setTitle($title) {
		if(!is_string($title)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->title = $title;
	}

	public function setVisible($isVisible) {
		if(!is_bool($isVisible)) {
			throw new \InvalidArgumentException(__METHOD__);
		}
		$this->isVisible = $isVisible;
	}

}