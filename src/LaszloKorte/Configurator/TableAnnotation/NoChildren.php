<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class NoChildren {
	private $fkNames;

	public function __construct($params) {
		$this->fkNames = isset($params['fks']) ? params['fks'] : null;
	}
}
