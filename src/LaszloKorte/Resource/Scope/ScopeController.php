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

final class ScopeController {
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
		if(isset($parameters[self::PARAM_NAME][self::PARAM_ENTITY])) {
			$flatConvention = new FlatConvention();

			$entityId = new Identifier($parameters[self::PARAM_NAME][self::PARAM_ENTITY]);
			$recordId = $this->idConverter->convert($parameters[self::PARAM_NAME][self::PARAM_ID]);
			$focus = new Focus($entityId, $recordId);
			
			$entity = $this->graph->entityById($entityId);
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
			return new RealScope(null, null, null, null);
		}
	}

	public function getVirtualScopeFor(RealScope $real, Identifier $entityId) {
		if(!$real->isSpecified()) {
			return new VirtualScope($real, null);
		}
		$entityChain = $this->graph->entityById($entityId)->getTreeChain();
		$scopeChain = $this->graph->entityById($real->getFocus()->getEntityId())->getTreeChain();

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

	public function getScopeSelector(VirtualScope $virtualScope) {
		return new ScopeSelector($virtualScope->getRealScope()->getQuery());
	}
}