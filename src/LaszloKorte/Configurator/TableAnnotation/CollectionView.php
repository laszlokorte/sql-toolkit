<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class CollectionView implements Annotation {
	public $name;

	public function __construct($name, ...$options) {
		$this->name = $name;
	}
}
