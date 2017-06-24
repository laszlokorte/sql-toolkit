<?php

namespace LaszloKorte\Resource\Pagination;

final class Pagination {
	private $currentPage;

	public function __construct(Page $currentPage) {
		$this->currentPage = $currentPage;
	}
}