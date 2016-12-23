<?php

namespace LaszloKorte\Schema\ColumnType;

interface ColumnType {
	public function coerce($value);
}