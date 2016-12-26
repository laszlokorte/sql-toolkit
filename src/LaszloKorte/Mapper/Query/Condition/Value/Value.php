<?php

namespace LaszloKorte\Mapper\Query\Condition\Value;

use LaszloKorte\Mapper\Record\Record;

interface Value {
	public function valueFor(Record $record);

	public function getRootType();
}
