<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

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
		return ['longitude' => $this->longitudeColumnId, 'latitude' => $this->latitudeColumnId];
	}

	public function getChildAssociations() {
		return [];
	}

	public function getParentAssociations() {
		return [];
	}
}