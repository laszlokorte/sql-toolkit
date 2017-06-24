<?php

namespace LaszloKorte\Resource\Pagination;

use LaszloKorte\Resource\Ordering\OrderingController;
use PDO;

final class PaginationController {
	private $pdo;
	private $orderingController;

	public function __construct(PDO $db, OrderingController $ordering) {
		$this->pdo = $pdo;
		$this->orderingController = $orderingController;
	}
}