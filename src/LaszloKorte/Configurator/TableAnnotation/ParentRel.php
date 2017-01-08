<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class ParentRel implements Annotation {
	public $parentName;

	public function __construct($params) {
		$this->parentName = $params['value'];
	}
}
