<?php

namespace LaszloKorte\Schema;

use SplObjectStorage;

final class IdentifierMap extends SplObjectStorage {
	public function getHash($o) {
		return $o->hash();
	}
}