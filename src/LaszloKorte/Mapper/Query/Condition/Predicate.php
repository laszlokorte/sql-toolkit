<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Record\Record;

interface Predicate {
	public function evalFor(Record $record);

	public function getRootType();

	public function getPaths();
}
