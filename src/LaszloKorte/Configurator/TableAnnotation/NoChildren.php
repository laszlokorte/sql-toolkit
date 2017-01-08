<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class NoChildren implements Annotation {
	public $fkNames;

	public function __construct($params) {
		$this->fkNames = isset($params['fks']) ? params['fks'] : null;
	}
}
