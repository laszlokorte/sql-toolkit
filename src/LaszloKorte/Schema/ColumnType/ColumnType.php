<?php

namespace LaszloKorte\Schema\ColumnType;

use Serializable;

interface ColumnType extends Serializable {
	public function coerce($value);
}