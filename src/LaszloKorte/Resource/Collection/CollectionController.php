<?php

namespace LaszloKorte\Resource\Collection;

use LaszloKorte\Graph\Graph;
use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Query\Record;

use LaszloKorte\Resource\Scope\ScopeController;

use PDO;

final class CollectionController {

	private $database;
	private $graph;
	private $paginationController;
	private $orderingController;

	public function __construct(PDO $db, Graph $graph, $paginationController, $orderingController) {
		$this->database = $db;
		$this->graph = $graph;
		$this->paginationController = $paginationController;
		$this->orderingController = $orderingController;
	}

	public function getCollection($entityId, $virtualScope, $parameters) {
		$entity = $this->graph->entityById($entityId);
		$paginator = $this->paginationController->getPaginator($entityId, $parameters);
		$ordering = $this->orderingController->getOrdering($entityId, $parameters);
		
		list($query, $records) = $this->loadRecords($entityId, $virtualScope, $paginator, $ordering);

		$pagination = $this->paginationController->getPagination($paginator, $records);

		return new Collection($records, $pagination, $ordering, $query);
	}

	private function loadRecords($entityId, $virtualScope, $paginator, $ordering) {
		$queryBuilder = new EntityQueryBuilder($this->graph->entityById($entityId));
		$queryBuilder->includeFieldColumns();
		$virtualScope->modifyQueryBuilder($queryBuilder);
		$paginator->modifyQueryBuilder($queryBuilder);
		$ordering->modifyQueryBuilder($queryBuilder);
		$query = $queryBuilder->getQuery();
		$paginator->modifyQuery($query);
		$ordering->modifyQuery($query);
		$stmt = $query->getPrepared($this->database);
		
		$queryBuilder->bind($stmt);
		$stmt->execute();

		return [$query, array_map(function($c) {
			return new Record($c);
		}, $ordering->transformResult(
			$paginator->transformResult($stmt->fetchAll())
		))];
	}
}