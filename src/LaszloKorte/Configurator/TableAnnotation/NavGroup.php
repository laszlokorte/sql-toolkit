<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class NavGroup {
	private $groupName;

	public function __construct($params) {
		$this->groupName = isset($params['name']) ? $params['name'] : $params['value'];
	}
}
