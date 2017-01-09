<?php

namespace LaszloKorte\Configurator\TableAnnotation;

/**
 * @Annotation 
 */
class CollectionView implements Annotation {
	public $name;
	public $params;

	public function __construct($params) {
		$this->name = $params['value'];
		$this->params = array_diff_key($params, array_flip(['value']));
	}
}
