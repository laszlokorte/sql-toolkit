<?php

namespace LaszloKorte\Resource\Controllers;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Query\Record;

use LaszloKorte\Resource\Scope\ScopeController;

use PDO;

final class CollectionController {

	private $database;
	private $queryBuilder;
	private $entity;
	private $scope;
	private $scopes;
	private $parameters;
	private $result = null;
	private $page = 0;
	private $export = false;

	public function __construct(PDO $db, Entity $entity, $parameters, $scope = NULL, $export = false) {
		$this->database = $db;
		$this->entity = $entity;
		$this->queryBuilder = new EntityQueryBuilder($entity);
		$this->scope = $scope;
		$this->parameters = $parameters;

		$this->page = $parameters['page'] ?? 1;
		$this->export = $export;

		$this->queryBuilder->includeFieldColumns();
		$this->scopes = new ScopeController($db, $entity, $parameters);
	}

	public function getParams() {
		return $this->parameters;
	}

	private function getQuery() {
		
		if(isset($this->parameters['order']['field'])) {
			$this->queryBuilder->sortByField($this->parameters['order']['field'], $this->parameters['order']['dir'] === 'asc');
		} else {
			$this->queryBuilder->sortDefault(($this->parameters['order']['dir']??null) === 'asc');
		}
		$query = $this->queryBuilder->getQuery();

		if(!$this->export) {
			$query->limit(21);
			$query->offset(($this->page - 1) * 20);
		}

		return $query;
	}

	public function isOrderedBy($field, $dir = 'asc') {
		return ($this->parameters['order']['dir']??null) === $dir && 
		($field === null && !isset($this->parameters['order']['field']) ||
		($field !== null && ($this->parameters['order']['field'] ?? null) == $field->id()));
	}

	public function records() {
		if($this->result === null) {
			$stmt = $this->getQuery()->getPrepared($this->database);

			$stmt->execute();

			$this->result = array_map(function($c) {
				return new Record($c);
			}, $stmt->fetchAll());

			if(empty($this->result) && $this->page > 1) {
				throw new NotFoundException();
			}
		}
		
		return $this->result;
	}

	public function scopes() {
		return $this->scopes;
	}

	public function hasPrevPage() {
		return $this->page > 1;
	}

	public function hasNextPage() {
		return count($this->records()) > 20;
	}

	public function getPage() {
		return $this->page;
	}

	public function sqlString() {
		return $this->getQuery();
	}
}