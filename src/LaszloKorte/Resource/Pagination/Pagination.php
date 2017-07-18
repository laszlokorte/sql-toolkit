<?php

namespace LaszloKorte\Resource\Pagination;

use LaszloKorte\Resource\Controllers\NotFoundException;

final class Pagination {
	private $currentPage;

	public function __construct(Page $currentPage, $hasNext) {
		$this->currentPage = $currentPage;
		$this->hasNext = $hasNext;
	}

	public function getOrdering() {
		return $this->ordering;
	}

	public function hasPrevPage() {
		return $this->currentPage->getNumber() > 1;
	}

	public function hasNextPage() {
		return $this->hasNext;
	}

	public function getPrevPage() {
		return new Page($this->currentPage->getNumber() - 1);
	}

	public function getNextPage() {
		return new Page($this->currentPage->getNumber() + 1);
	}

	public function getPage() {
		return $this->currentPage;
	}

	public function buildParams($params, $page = NULL) {
		return $params->replace('page', ($page ?: $this->currentPage)->getNumber());
	}

	public function resetParams($params) {
		return $params->remove('page');
	}

}