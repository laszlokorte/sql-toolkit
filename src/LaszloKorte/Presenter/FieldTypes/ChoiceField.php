<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class ChoiceField implements FieldType {
	private $choices = [];
	private $columnId;

}