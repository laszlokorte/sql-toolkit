<?php

namespace LaszloKorte\Resource\Scope;

use LaszloKorte\Resource\IdConverter;
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
	private $scopeRecord;

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
			$query = $queryBuilder->getQuery();
			$query->flatNames();
			$stmt = $query->getPrepared($database);
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
			if($entityLink->isLast()) {
				break;
			}
			$source = $entityLink->source();
			$entity = $entityLink->target();
			$queryBuilder = new EntityQueryBuilder($entity);
			$queryBuilder->includeDisplayColumns();
			if($source) {
				$queryBuilder->scopeToParent();
			}

			$query = $queryBuilder->getQuery();
			$stmt = $query->getPrepared($database);
			if($source) {
				$queryBuilder->bindParent($stmt, $record->id($entity->parentEntity(), true));
			}
			$stmt->execute();
			$scopeChoices = array_map(function($c) {
				return new Record($c);
			}, $stmt->fetchAll());

			if($scopeLink === NULL) {
				$scopes[]= new Scope($entity, $record, $scopeChoices, $query, false);
			} else {
				$scopeTarget = $scopeLink->target();
				if($entity->id() != $scopeTarget->id()) {
					$scopes[]= new Scope($entity, $record, $scopeChoices, $query, false);
				} else {
					$scopes[]= new Scope($entity, $record, $scopeChoices, $query, true);
					continue;
				}
			}
			break;
		}

		$this->scopes = $scopes;
	}

	public function getIterator() {
		return new ArrayIterator($this->scopes);
	}

	public function prepare($queryBuilder, $stmt) {
		if($this->scopeRecord) {
			//$queryBuilder->bindParent($stmt, $this->scopeRecord->id($queryBuilder->getEntity()->parentEntity(), true));
		}
	}

	public function buildQueryAfter($queryBuilder) {
		if($this->scopeRecord) {
			//s$queryBuilder->scopeToParent();
		}
	}
}