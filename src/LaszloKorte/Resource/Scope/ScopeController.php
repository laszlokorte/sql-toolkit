<?php

namespace LaszloKorte\Resource\Scope;

use LaszloKorte\Resource\IdConverter;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Query\Record;
use LaszloKorte\Resource\Query\Scope as QueryScope;

use PDO;
use IteratorAggregate;
use ArrayIterator;

final class ScopeController implements IteratorAggregate {

	private $database;
	private $scopes;
	private $scopeRecord;
	private $queryScope;
	private $query;

	public function __construct(PDO $database, Entity $entity, $parameters) {
		$this->database = $database;
		$this->entity = $entity;
		$this->parameters = $parameters;

		if(isset($parameters['scope']['entity'])) {
			$idConverter = new IdConverter();
			$scopeId = $idConverter->convert($parameters['scope']['id']);
			$scopeEntity = $entity->otherEntity(new Identifier($parameters['scope']['entity']));
			$scopeChain = $scopeEntity->getTreeChain();
			
			$queryBuilder = new EntityQueryBuilder($scopeEntity);
			$queryBuilder->oneById();
			$queryBuilder->includeParents();
			$queryBuilder->includeDisplayColumns();
			$this->query = $queryBuilder->getQuery();
			$this->query->flatNames();
			$stmt = $this->query->getPrepared($database);
			$queryBuilder->bindId($stmt, $scopeId);
			$stmt->execute();
			$r = $stmt->fetch();
			$record = new Record($r, TRUE);
		} else {
			$record = NULL;
			$scopeChain = new \ArrayIterator([]);
		}
		$this->scopeRecord = $record;

		$entityChain = $entity->getTreeChain();


		$scopes = [];

		foreach (
			array_map(null, 
				iterator_to_array($entityChain), 
				iterator_to_array($scopeChain)
			) AS 
			list($entityLink, $scopeLink)
		) {
			$entity = $entityLink->target();
			if($entityLink->isLast()) {
				if ($backLinks = $entityLink->backLinks()) {
					$this->queryScope = new QueryScope($backLinks, $entity->idColumns());
				}
				break;
			}
			$source = $entityLink->source();
			$queryBuilder = new EntityQueryBuilder($entity);
			$queryBuilder->includeDisplayColumns();
			if($source) {
				$queryBuilder->scope(new QueryScope([$entity->parentAssociation()->toLink()], $entity->parentEntity()->idColumns()));
			}

			$query = $queryBuilder->getQuery();
			$stmt = $query->getPrepared($database);
			if($source) {
				$queryBuilder->bindScope($stmt, $record->id($entity->parentEntity(), true));
			}
			$stmt->execute();
			$scopeChoices = array_map(function($c) {
				return new Record($c);
			}, $stmt->fetchAll());

			if($scopeLink === NULL) {
				$scopes[]= new ScopeItem($entity, $record, $scopeChoices, $query, false);
			} else {
				$scopeTarget = $scopeLink->target();
				if($entity->id() != $scopeTarget->id()) {
					$scopes[]= new ScopeItem($entity, $record, $scopeChoices, $query, false);
				} else {
					$scopes[]= new ScopeItem($entity, $record, $scopeChoices, $query, true);
					continue;
				}
			}
			if($backLinks = $entityLink->backLinks()) {
				$this->queryScope = new QueryScope($backLinks, $entity->idColumns());
			}
			break;
		}

		$this->scopes = $scopes;
	}

	public function getIterator() {
		return new ArrayIterator($this->scopes);
	}

	public function prepare($queryBuilder, $stmt) {
		if($this->queryScope) {
			$queryBuilder->bindScope($stmt, $this->scopeRecord->id($this->entity->otherEntity($this->queryScope->getTargetTable()), true));
		}
	}

	public function buildQueryAfter($queryBuilder) {
		if($this->queryScope) {
			$queryBuilder->scope($this->queryScope);
		}
	}

	public function getQuery() {
		return $this->query;
	}
}