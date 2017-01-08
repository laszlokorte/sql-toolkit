<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class NavGroup implements Annotation {
	public $groupName;

	public function __construct($params) {
		$this->groupName = isset($params['name']) ? $params['name'] : $params['value'];
	}
}
