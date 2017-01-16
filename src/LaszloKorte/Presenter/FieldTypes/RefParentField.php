<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class RefParentField implements FieldType {
	private $entityId;
	private $fkOwnColumns;
}