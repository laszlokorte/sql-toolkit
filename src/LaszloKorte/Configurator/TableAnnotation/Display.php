<?php

namespace LaszloKorte\Configurator\TableAnnotation;

use LaszloKorte\Configurator\Aspect\Template;

/**
 * @Annotation 
 */
class Display implements Annotation, Template {
	public $templateString;
	private $processed;

	public function __construct($params) {
		$this->templateString = $params['value'];
	}

	public function getString() {
		return $this->templateString;
	}

	public function setProcessedTemplate($tpl) {
		$this->processed = $tpl;
	}
	
	public function getProcessedTemplate() {
		return $this->processed;
	}
}
