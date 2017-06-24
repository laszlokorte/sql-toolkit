<?php

namespace LaszloKorte\Resource\Scene;

use LaszloKorte\Graph\Identifier;

use PDO;
use LaszloKorte\Resource\Ordering\OrderingController;
use LaszloKorte\Resource\Navigation\NavigationController;
use LaszloKorte\Resource\Collection\CollectionController;
use LaszloKorte\Resource\Pagination\PaginationController;
use LaszloKorte\Resource\Scope\ScopeController;
use LaszloKorte\Resource\ParameterBag;
use LaszloKorte\Graph\Template\Renderer;

final class CollectionScene {

	private $db;
	private $graph;
	private $renderer;
	private $idConverter;

	private $scopeController;
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
		$this->navigationController = new NavigationController($graph);
		$this->orderingController = new OrderingController();
		$this->paginationController = new PaginationController();
		$this->collectionController = new CollectionController($db, $graph, $this->orderingController, $this->paginationController);
	}

	public function load(Identifier $entityId, ParameterBag $parameters) {
		$realScope = $this->scopeController->getRealScope($parameters);
		$virtualScope = $this->scopeController->getVirtualScopeFor($realScope, $entityId);
		$navigation = $this->navigationController->getNavigation($entityId, $parameters, $realScope);
		$collection = $this->collectionController->getCollection($entityId, $parameters);

		return [
	        'navigation' => $navigation,
	        'scope' => $scope,
	        'collection' => $collection,
	        'templateRenderer' => $this->renderer,
    	];
	}
}