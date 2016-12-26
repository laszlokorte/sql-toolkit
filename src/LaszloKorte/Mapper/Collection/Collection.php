<?php

namespace LaszloKorte\Mapper\Collection;

use LaszloKorte\Mapper\Type;
use LaszloKorte\Mapper\Query\Condition\Predicate;
use LaszloKorte\Mapper\Query\Ordering;

use IteratorAggregate;
use Countable;

interface Collection extends IteratorAggregate, Countable {

	public function getType();

	public function filter(Predicate $cond);

	public function expand(Predicate $cond);

	public function orderBy(Ordering ...$cond);

	public function take($limit);

	public function skip($offset);

	public function toArray();

	public function get($idx);
}
