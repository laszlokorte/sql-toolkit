<?php

namespace LaszloKorte\Mapper;

use PDO;

class DataSource {

	private $connection;

	public function __construct(PDO $connection) {
		$this->connection = $connection;
	}

}
