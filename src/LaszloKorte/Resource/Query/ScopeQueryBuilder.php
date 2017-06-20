<?php

namespace LaszloKorte\Resource\Query;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Entity;

final class ScopeQueryBuilder {

	private $entity;

	public function __construct(Entity $entity) {
		$this->entity = $entity;
	}

	public function getQuery() {
		$table = $this->entity->id();
		$query = new ScopeQuery($table);

		return $query;
	}
}