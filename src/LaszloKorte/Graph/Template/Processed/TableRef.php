<?php

namespace LaszloKorte\Graph\Template\Processed;

use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\Record;

use Serializable;

final class TableRef implements Serializable {

	private $tablePath;

	public function __construct(TablePath $tablePath) {
		$this->tablePath = $tablePath;
	}

	public function __toString() {
		return (string) $this->tablePath;
	}

	public function getPath() {
		return $this->tablePath;
	}

	public function serialize() {
		return serialize([
			$this->tablePath,
		]);
	}

	public function unserialize($data) {
		list(
			$this->tablePath,
		) = unserialize($data);
	}
}