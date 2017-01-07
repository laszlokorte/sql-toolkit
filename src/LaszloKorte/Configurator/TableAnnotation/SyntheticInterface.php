<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class SyntheticInterface {
	private $interfaceName;
	private $params;

	public function __construct($params) {
		$this->interfaceName = $params['value'];
		$this->params = $params;
	}
}
