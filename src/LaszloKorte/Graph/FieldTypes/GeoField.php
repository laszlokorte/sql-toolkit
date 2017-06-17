<?php

namespace LaszloKorte\Graph\FieldTypes;

use LaszloKorte\Graph\FieldTypes\FieldType;
use LaszloKorte\Graph\Identifier;

use Serializable;

class GeoField implements FieldType, Serializable {
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

	public function serialize() {
		return serialize([
			$this->longitudeColumnId,
			$this->latitudeColumnId,
		]);
	}

	public function unserialize($data) {
		list(
			$this->longitudeColumnId,
			$this->latitudeColumnId,
		) = unserialize($data);
	}
}