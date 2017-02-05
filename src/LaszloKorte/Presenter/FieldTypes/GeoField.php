<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;
use LaszloKorte\Presenter\Identifier;

class GeoField implements FieldType {
	private $longitudeColumnId;
	private $latitudeColumnId;

	public function __construct(Identifier $longitudeColumnId, Identifier $latitudeColumnId) {
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