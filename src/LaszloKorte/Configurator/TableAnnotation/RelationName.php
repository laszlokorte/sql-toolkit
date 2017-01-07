<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class RelationName {
	private $fkName;
	private $singular;
	private $plural;

	public function __construct($params) {
		$this->fkName = $params['fk'] ?? $params['value'];
		$this->singular = $params['singular'];
		$this->plural = $params['plural'];
	}
}
