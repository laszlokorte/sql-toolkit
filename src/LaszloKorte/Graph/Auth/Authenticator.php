<?php

namespace LaszloKorte\Graph\Auth;

use LaszloKorte\Graph\Identifier;

final class Authenticator {
	private $tableName;
	private $loginColumn;
	private $passwordColumn;

	public function __construct(Identifier $tableName, Identifier $loginColumn, Identifier $passwordColumn) {
		$this->tableName = $tableName;
		$this->loginColumn = $loginColumn;
		$this->passwordColumn = $passwordColumn;
	}

	public function getTable() {
		return $this->tableName;
	}

	public function getLoginColumn() {
		return $this->loginColumn;
	}

	public function getPasswordColumn() {
		return $this->passwordColumn;
	}
}