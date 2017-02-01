<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class GeoField implements FieldType {
	private $longitudeColumnId;
	private $latitudeColumnId;

	public function __construct($longitudeColumnId, $latitudeColumnId) {
		$this->longitudeColumnId = $longitudeColumnId;
		$this->latitudeColumnId = $latitudeColumnId;
	}

	public function getTemplateName() {
		return 'geo';
	}

	public function getRelatedColumns() {
		return [$this->longitudeColumnId, $this->latitudeColumnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}
}