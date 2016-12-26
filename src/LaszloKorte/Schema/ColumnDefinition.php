<?php

namespace LaszloKorte\Schema;

final class ColumnDefinition {
	private $dataType;
	private $allowNull;
	private $defaultValue;

	public function __construct($dataType, $allowNull, $defaultValue, $comment) {
		$this->dataType = $dataType;
		$this->allowNull = $allowNull;
		$this->defaultValue = $defaultValue;
		$this->comment = $comment;
	}

	public function getType() {
		return $this->dataType;
	}

	public function isNullable() {
		return $this->allowNull;
	}

	public function getComment() {
		return $this->comment;
	}

	public function getDefaultValue() {
		return $this->defaultValue;
	}
}