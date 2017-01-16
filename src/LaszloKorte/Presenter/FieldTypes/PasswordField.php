<?php

namespace LaszloKorte\Presenter\FieldTypes;

use LaszloKorte\Presenter\FieldTypes\FieldType;

class PasswordField implements FieldType {
	private $requireRepeat = false;
}