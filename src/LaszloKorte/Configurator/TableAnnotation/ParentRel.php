<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class ParentRel {
	private $parentName;

	public function __construct($params) {
		$this->parentName = $params['value'];
	}
}
