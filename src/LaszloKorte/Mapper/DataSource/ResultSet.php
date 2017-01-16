<?php

namespace LaszloKorte\Mapper\DataSource;

use PDOStatement;
use Iterator;
use PDO;

final class ResultSet implements Iterator {

	private $stmt;
	private $cache;
	private $next;
	private $executed;

	public function __construct(PDOStatement $stmt) {
		$this->stmt = $stmt;
		$this->cache = [];
		$this->next = NULL;
		$this->executed = FALSE;
	}

	public function rewind() {
		reset($this->cache);
		$this->next();
	}

	public function current() {
		return $this->next[1];
	}

	public function key() {
		return $this->next[0];
	}

	public function next() {
		$this->next = each($this->cache);

		if (FALSE === $this->next) {
			$this->doFetch();
		}
	}

	private function doFetch() {
		if($this->executed === FALSE) {
			$this->executed = $this->stmt->execute();
		}

		$row = $this->stmt->fetch(PDO::FETCH_OBJ);

		if ($row) {
			$this->cache[] = $row;
		}

		$this->next = each($this->cache);
	}

	public function valid() {
		return (FALSE !== $this->next);
	}

}
