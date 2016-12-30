<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class ParentRel {
	private $parentName;

	public function __construct($parentName) {
		$this->parentName = $parentName;
	}
}
