<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class SyntaxField implements FieldType {
	private $grammar;
	private $columnName;
}