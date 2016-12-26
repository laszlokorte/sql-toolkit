<?php

namespace LaszloKorte\Mapper\Path;

use LaszloKorte\Mapper\Path\DSL\Aggregating;
use LaszloKorte\Mapper\Path\DSL\Comparing;
use LaszloKorte\Mapper\Path\DSL\Ordering;

interface FieldPath extends Path, Aggregating, Comparing, Ordering {
	public function getField();
}