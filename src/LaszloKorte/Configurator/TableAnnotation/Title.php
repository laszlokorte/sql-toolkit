<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Title {
	private $title;

	public function __construct($title) {
		$this->title = $title;
	}
}
