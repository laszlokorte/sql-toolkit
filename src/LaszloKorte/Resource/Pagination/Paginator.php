<?php

namespace LaszloKorte\Resource\Pagination;

use LaszloKorte\Resource\Controllers\NotFoundException;

final class Paginator {
	private $currentPage;
	private $perPage = 20;

	public function __construct(Page $currentPage) {
		$this->currentPage = $currentPage;
	}

	public function getPage() {
		return $this->currentPage;
	}

	public function getPerPage() {
		return $this->perPage;
	}

	public function modifyQueryBuilder($queryBuilder) {
	}

	public function modifyQuery($query) {
		$query->limit($this->perPage + 1);
		$query->offset($this->currentPage->offset($this->perPage));
		
	}

	public function transformResult($result) {
		if(empty($result) && $this->currentPage->getNumber() > 1) {
			throw new NotFoundException();
		}
		return $result;
	}

}