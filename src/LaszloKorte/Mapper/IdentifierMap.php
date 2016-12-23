<?php

namespace LaszloKorte\Mapper;

use SplObjectStorage;

class IdentifierMap extends SplObjectStorage {
	public function getHash($o) {
		return $o->hash();
	}
}