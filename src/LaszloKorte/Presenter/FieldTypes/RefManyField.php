<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class RefManyField implements FieldType {
	const STYLE_TAGS = 1;
	const STYLE_DETAILED = 2;

	private $joinEntityId;
	private $fkOwnColumns;
	private $fkOtherColumns;
}