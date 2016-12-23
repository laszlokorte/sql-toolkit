<?php

namespace LaszloKorte\Mapper;

class Join {
	static const LEFT = 'JOIN_LEFT';
	static const RIGHT = 'JOIN_RIGHT';
	static const OUTER = 'JOIN_OUTER';
	static const INNER = 'JOIN_INNER';

	private $type;
}
