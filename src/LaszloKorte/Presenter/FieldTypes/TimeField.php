<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class TimeField implements FieldType {
	private $includeSeconds = false;
	private $columnName;
}