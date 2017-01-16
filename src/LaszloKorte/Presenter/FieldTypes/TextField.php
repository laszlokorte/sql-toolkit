<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class TextField implements FieldType {
	const TYPE_SINGLE_LINE = 1;
	const TYPE_MULTI_LINE = 2;

	private $type;
	private $columnName;
}