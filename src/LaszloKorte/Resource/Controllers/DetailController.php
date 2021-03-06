<?php

namespace LaszloKorte\Resource\Controllers;

use LaszloKorte\Graph\Entity;
use LaszloKorte\Graph\Association\ChildAssociation;
use LaszloKorte\Resource\Query\EntityQueryBuilder;
use LaszloKorte\Resource\Query\Record;

use PDO;

final class DetailController {

	private $database;
	private $queryBuilder;
	private $entity;
	private $id;
	private $scope;
	private $parameters;
	private $result = null;
	private $page = 0;

	public function __construct(PDO $db, Entity $entity, $id, $parameters, $scope = NULL) {
		$this->database = $db;
		$this->entity = $entity;
		$this->id = $id;
		$this->queryBuilder = new EntityQueryBuilder($entity);
		$this->scope = $scope;
		$this->parameters = $parameters;

		$this->queryBuilder->includeFieldColumns();
		$this->queryBuilder->oneById($id);
		$this->queryBuilder->includeDisplayColumns();

	}
	
	public function getParams() {
		return $this->parameters;
	}

	private function getQuery() {
		$query = $this->queryBuilder->getQuery();

		return $query;
	}

	public function record() {
		if($this->result === null) {
			$stmt = $this->getQuery()->getPrepared($this->database);

			$this->queryBuilder->bind($stmt);
			$stmt->execute();

			$this->result = new Record($stmt->fetch());

			if($this->result === false) {
				throw new NotFoundException();
			}
		}
		
		return $this->result;
	}

	public function sqlString() {
		return $this->getQuery();
	}

	public function children(ChildAssociation $assoc) {
		return new CollectionController($this->$database, $assoc->getTargetEntity(), $this->parameters, [(string)$assoc->getName()]);
	}
}