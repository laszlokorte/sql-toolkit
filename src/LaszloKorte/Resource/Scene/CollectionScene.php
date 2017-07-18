<?php

namespace LaszloKorte\Resource\Scene;

use LaszloKorte\Graph\Identifier;
use LaszloKorte\Graph\Graph;

use PDO;
use LaszloKorte\Resource\Ordering\OrderingController;
use LaszloKorte\Resource\Navigation\NavigationController;
use LaszloKorte\Resource\Collection\CollectionController;
use LaszloKorte\Resource\Pagination\PaginationController;
use LaszloKorte\Resource\CollectionView\CollectionViewController;
use LaszloKorte\Resource\Scope\ScopeController;
use LaszloKorte\Resource\ParameterBag;
use LaszloKorte\Graph\Template\Renderer;

final class CollectionScene {

	private $db;
	private $graph;
	private $renderer;
	private $idConverter;

	private $scopeController;
	private $collectionViewController;
	private $navigationController;
	private $orderingController;
	private $paginationController;
	private $collectionController;

	public function __construct(PDO $db, Graph $graph, Renderer $renderer, $idConverter) {
		$this->db = $db;
		$this->graph = $graph;
		$this->renderer = $renderer;
		$this->idConverter = $idConverter;

		$this->scopeController = new ScopeController($db, $graph, $idConverter);
		$this->collectionViewController = new CollectionViewController($graph);
		$this->navigationController = new NavigationController($graph);
		$this->orderingController = new OrderingController($graph);
		$this->paginationController = new PaginationController($db);
		$this->collectionController = new CollectionController($db, $graph, $this->paginationController, $this->orderingController);
	}

	public function load(Identifier $entityId, ParameterBag $parameters) {
		$entity = $this->graph->entityById($entityId);
		$realScope = $this->scopeController->getRealScope($parameters);
		$virtualScope = $this->scopeController->getVirtualScopeFor($realScope, $entityId);
		$scopeSelector = $this->scopeController->getScopeSelector($virtualScope);
		$navigation = $this->navigationController->getNavigation($entityId, $parameters, $realScope);
		$collection = $this->collectionController->getCollection($entityId, $virtualScope, $parameters);
		$collectionView = $this->collectionViewController->getView($entityId, $parameters, $collection);

		return [
			'params' => $parameters,
			'entity' => $entity,
	        'navigation' => $navigation,
	        'scopeSelector' => $scopeSelector,
	        'collectionView' => $collectionView,
	        'templateRenderer' => $this->renderer,
    	];
	}
}