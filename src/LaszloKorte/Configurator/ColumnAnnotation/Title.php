<?php

namespace LaszloKorte\Configurator\ColumnAnnotation;

/**
 * @Annotation 
 */
class Title implements Annotation {
	public $title;

	public function __construct($params) {
		$this->title = $params['value'];
	}
}
