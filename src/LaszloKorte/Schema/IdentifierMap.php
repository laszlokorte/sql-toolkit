<?php

namespace LaszloKorte\Schema;

use SplObjectStorage;
use Serializable;

final class IdentifierMap extends SplObjectStorage {
	public function getHash($o) {
		return $o->hash();
	}

}