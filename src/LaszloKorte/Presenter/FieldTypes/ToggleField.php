<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class ToggleField implements FieldType {
	const TYPE_RADIO = 1;
	const TYPE_CHECKBOX = 2;

	private $type;
	private $columnName;
}