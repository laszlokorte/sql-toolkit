<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class RefChildrenField implements FieldType {
	private $entityId;
	private $fkOtherColumns;
}