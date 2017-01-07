<?php

namespace LaszloKorte\Presenter;

use SplObjectStorage;

final class IdentifierMap extends SplObjectStorage {
	public function getHash($o) {
		return $o->hash();
	}
}