<?php

namespace LaszloKorte\Resource\Controllers;

use LaszloKorte\Graph\Entity;
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
		$this->queryBuilder->oneById();
		$this->queryBuilder->includeDisplayColumns();

	}
	
	public function getParams() {
		return $this->parameters;
	}

	private function getQuery() {
		$query = $this->queryBuilder->getQuery();
		$query->limit(1);

		return $query;
	}

	public function record() {
		if($this->result === null) {
			$stmt = $this->getQuery()->getPrepared($this->database);

			$this->queryBuilder->bindId($stmt, $this->id);
			$stmt->execute();

			$stmt->setFetchMode(PDO::FETCH_CLASS, Record::class);
			$this->result = $stmt->fetch();

			if($this->result === false) {
				throw new NotFoundException();
			}
		}
		
		return $this->result;
	}

	public function sqlString() {
		return $this->getQuery();
	}
}