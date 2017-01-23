<?php

namespace LaszloKorte\Presenter;

use LaszloKorte\Configurator\ColumnAnnotation as CA;

interface FieldBuilder {
	

	public function reportUnknownAnnotation($annotation);

	public function requireUnique(CA\Annotation $a);

	public function setAggregatable($isAggregatable);

	public function setDescription($description);

	public function setCollectionVisible($visible);

	public function setType($type, array $params);

	public function setLinked($isLinked);

	public function setSecret($isSecret);

	public function setTitle($title);

	public function setVisible($isVisible);

	public function buildField($ab, $entityDef);

}