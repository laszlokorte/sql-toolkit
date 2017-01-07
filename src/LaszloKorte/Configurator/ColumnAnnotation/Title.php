<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Title {
	private $title;

	public function __construct($params) {
		$this->title = $params['value'];
	}
}
