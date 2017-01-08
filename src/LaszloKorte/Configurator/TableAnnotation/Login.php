<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class Login implements Annotation {
	public $loginColumn;
	public $passwordColumn;

	public function __construct($params) {
		$this->loginColumn = isset($params['login']) ? $params['login'] : null;
		$this->passwordColumn = isset($params['password']) ? $params['password'] : null;
	}
}
