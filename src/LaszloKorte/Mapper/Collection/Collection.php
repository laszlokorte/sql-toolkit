<?php

namespace LaszloKorte\Mapper\Collection;

use LaszloKorte\Mapper\Type;
use LaszloKorte\Mapper\Query\Condition;
use LaszloKorte\Mapper\Query\Ordering;

use IteratorAggregate;
use Countable;

class Collection extends IteratorAggregate, Countable {

	public function getType();

	public function filter(Condition $cond);

	public function expand(Condition $cond);

	public function orderBy(Ordering $cond);

	public function take($limit);

	public function skip($offset);

	public function toArray();

	public function get($idx);
}
