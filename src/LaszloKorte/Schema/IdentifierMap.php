<?php

namespace LaszloKorte\Schema;

use SplObjectStorage;

class IdentifierMap extends SplObjectStorage {
	public function getHash($o) {
		return $o->hash();
	}
}