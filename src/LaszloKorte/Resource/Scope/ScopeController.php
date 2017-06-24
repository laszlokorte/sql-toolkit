<?php

namespace LaszloKorte\Resource\Scope;

use LaszloKorte\Resource\IdConverter;
use LaszloKorte\Graph\Graph;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Path\TablePath;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Query\Record;
use LaszloKorte\Resource\Query\Scope as QueryScope;
use LaszloKorte\Resource\Query\Naming\FlatConvention;

use PDO;
use IteratorAggregate;
use ArrayIterator;

final class ScopeController implements IteratorAggregate {
	const PARAM_NAME = 'scope';
	const PARAM_ENTITY = 'entity';
	const PARAM_ID = 'id';

	private $database;
	private $graph;
	private $idConverter;

	public function __construct(PDO $database, Graph $graph, $idConverter) {
		$this->database = $database;
		$this->graph = $graph;
		$this->idConverter = $idConverter;
	}

	public function getRealScope($parameters) {
		if(isset($parameters[self::PARAM_NAME]['entity'])) {
			$flatConvention = new FlatConvention();

			$entityId = new Identifier($parameters[self::PARAM_NAME][self::PARAM_ENTITY]);
			$recordId = $this->idConverter->convert($parameters[self::PARAM_NAME][self::PARAM_ID]);
			$focus = new Focus($entityId, $recordId);
			
			$entity = $this->graph->entity((string)$entityId);
			$scopeChain = $entity->getTreeChain();
			
			$queryBuilder = new EntityQueryBuilder($entity);
			$queryBuilder->oneById();
			$queryBuilder->includeParents();
			$queryBuilder->includeDisplayColumns();

			$scopesQuery = $queryBuilder->getQuery($flatConvention);
			$stmt = $scopesQuery->getPrepared($database);
			$queryBuilder->bindId($stmt, $recordId);
			$stmt->execute();
			$row = $stmt->fetch();

			return new RealScope($focus, $row, $scopesQuery, $flatConvention);
		} else {
			return NULL;
		}
	}

	public function getVirtualScopeFor(RealScope $real, Identifier $entityId) {
		$entityChain = $this->graph->entity($entityId)->getTreeChain();
		$scopeChain = $this->graph->entity($real->getEntityId())->getTreeChain();

		$scopes = [];

		foreach (
			array_map(null, 
				iterator_to_array($entityChain), 
				iterator_to_array($scopeChain)
			) AS 
			list($entityLink, $scopeLink)
		) {
			$entity = $entityLink->entity();
			if($entityLink->isLeaf()) {
				if ($backLinks = $entityLink->backLinks()) {
					$queryScope = new QueryScope($backLinks, $entity->idColumns());
				}
				break;
			}
			$source = $entityLink->parent();
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
				$scopeTarget = $scopeLink->entity();
				if($entity->id() != $scopeTarget->id()) {
					$scopes[]= new ScopeItem($entity, $record, $scopeChoices, $query, false);
				} else {
					$scopes[]= new ScopeItem($entity, $record, $scopeChoices, $query, true);
					continue;
				}
			}
			if($backLinks = $entityLink->backLinks()) {
				$queryScope = new QueryScope($backLinks, $entity->idColumns());
			}
			break;
		}
	}

	public function getIterator() {
		return new ArrayIterator($this->scopes);
	}

	public function prepare($queryBuilder, $stmt) {
		if($this->queryScope) {
			$queryBuilder->bindScope($stmt, $this->scopeRecord->id($this->entity->graph()->entity((string)$this->queryScope->getTargetTable()), true));
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