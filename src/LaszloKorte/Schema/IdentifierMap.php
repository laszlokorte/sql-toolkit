<?php

namespace LaszloKorte\Schema;

use SplObjectStorage;
use Serializable;

final class IdentifierMap extends SplObjectStorage implements Serializable {
	public function getHash($o) {
		return $o->hash();
	}

}