<?php

namespace LaszloKorte\Resource\Scope;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Query\Record;

use PDO;
use IteratorAggregate;
use ArrayIterator;

final class ScopeController implements IteratorAggregate {

	private $database;
	private $scopes;

	public function __construct(PDO $database, Entity $entity, $parameters) {
		$this->database = $database;
		$this->entity = $entity;
		$this->parameters = $parameters;

		if(isset($parameters['scope']['entity'])) {
			$scopeEntity = $entity->otherEntity(new Identifier($parameters['scope']['entity']));
			$scopeChain = $scopeEntity->getTreeChain();
			$entityChain = $entity->getTreeChain();

			$scopeChain = $entityChain->intersectWith($scopeChain);
		} else {
			$scopeChain = $entity->getTreeChain()->getRoot();
		}

		$this->scopes = array_map(function($chainLink) use($database, $parameters) {
			$entity = $chainLink->target();
			$queryBuilder = new EntityQueryBuilder($entity);
			$queryBuilder->includeDisplayColumns();
			$query = $queryBuilder->getQuery();
			$stmt = $query->getPrepared($database);
			$stmt->execute();
			$scopeChoices = array_map(function($c) {
				return new Record($c);
			}, $stmt->fetchAll());
			$active = ($parameters['scope']['entity'] ?? NULL) == $entity->id();
			return new Scope($entity, $scopeChoices, $query, $active);
		}, iterator_to_array($scopeChain));
	}

	public function getIterator() {
		return new ArrayIterator($this->scopes);
	}
}