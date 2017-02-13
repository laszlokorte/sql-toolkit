<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\PathLink;

final class Aggregation {
	const TYPE_COUNT = 'COUNT';

	private $type;
	private $name;
	private $link;

	public function __construct($type, Identifier $name, PathLink $link) {
		$this->type = $type;
		$this->name = $name;
		$this->link = $link;
	}

	public function getType() {
		return $this->type;
	}

	public function getName() {
		return $this->name;
	}

	public function getLink() {
		return $this->link;
	}
}