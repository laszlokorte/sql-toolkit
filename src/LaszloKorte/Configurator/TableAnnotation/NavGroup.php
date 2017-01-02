<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class NavGroup {
	private $groupName;

	public function __construct($groupName) {
		$this->groupName = $groupName;
	}
}
