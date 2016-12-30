<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class CollectionView {
	private $name;

	public function __construct($name, ...$options) {
		$this->name = $name;
	}
}
