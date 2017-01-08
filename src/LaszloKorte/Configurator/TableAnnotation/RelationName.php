<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class RelationName implements Annotation {
	public $fkName;
	public $singular;
	public $plural;

	public function __construct($params) {
		$this->fkName = $params['fk'] ?? $params['value'];
		$this->singular = $params['singular'];
		$this->plural = $params['plural'];
	}
}
