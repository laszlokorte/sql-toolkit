<?php

namespace LaszloKorte\Resource\Pagination;

use PDO;

final class PaginationController {
	private $db;

	public function __construct(PDO $db) {
		$this->db = $db;
	}

	public function getPaginator($entityId, $parameters) {
		$pageNumber = intval($parameters['page'] ?? 1);
		if($pageNumber < 1) {
			throw new \Exception("Page must be > 0");
		}
		$page = new Page($pageNumber);
		
		return new Paginator($page);
	}

	public function getPagination($paginator, $records) {
		return new Pagination($paginator->getPage(), count($records) > $paginator->getPerPage());
	}
}