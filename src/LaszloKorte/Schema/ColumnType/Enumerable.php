<?php

namespace LaszloKorte\Schema\ColumnType;

interface Enumerable {
	public function getOptions();

	public function allowsMultiple();
}