<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class GeoField implements FieldType {
	private $longitudeColumn;
	private $latitudeColumn;
}