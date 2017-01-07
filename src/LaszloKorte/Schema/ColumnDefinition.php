<?php

namespace LaszloKorte\Schema;

use Serializable;

final class ColumnDefinition implements Serializable {
	private $dataType;
	private $allowNull;
	private $defaultValue;
	private $comment;

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

	public function serialize() {
		return serialize([
			$this->dataType,
			$this->allowNull,
			$this->defaultValue,
			$this->comment,
		]);
	}

	public function unserialize($data) {
		list(
			$this->dataType,
			$this->allowNull,
			$this->defaultValue,
			$this->comment
		) = unserialize($data);
	}
}