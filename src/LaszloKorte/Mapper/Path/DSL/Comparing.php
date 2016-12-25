<?php

namespace LaszloKorte\Mapper\Path\DSL;

interface Comparing {
	public function eq($other);

	public function neq($other);

	public function lt($other);

	public function gt($other);
	
	public function lte($other);

	public function gte($other);

	public function like($other);

	public function in($list);
}