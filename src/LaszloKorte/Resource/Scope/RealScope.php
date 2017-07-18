<?php

namespace LaszloKorte\Resource\Scope;

final class RealScope {
	private $focus;
	private $data;
	private $query;
	private $namingConvention;

	public function __construct($focus, $data, $query, $namingConvention) {
		$this->focus = $focus;
		$this->data = $data;
		$this->query = $query;
		$this->namingConvention = $namingConvention;
	}

	public function getQuery() {
		return $this->query;
	}

	public function isSpecified() {
		return $this->focus !== NULL;
	}

	public function getFocus() {
		return $this->focus;
	}

	public function id($entityId) {
		return array_map(function($col) use($entityId) {
			$path = new OwnColumnPath($entityId, $col);
			$propName = $this->namingConvention->columnName($path);
			return $this->data->$propName;
		}, $e->idColumns());
	}
}