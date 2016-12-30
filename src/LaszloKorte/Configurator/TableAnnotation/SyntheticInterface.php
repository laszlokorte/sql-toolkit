<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class SyntheticInterface {
	private $interfaceName;
	private $params;

	public function __construct($interfaceName, ...$params) {
		$this->interfaceName = $interfaceName;
		$this->params = $params;
	}
}
