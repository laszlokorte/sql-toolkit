<?php

namespace LaszloKorte\Mapper\Path\DSL;

interface Aggregating {
	public function count();

	public function avg();

	public function min();

	public function max();
	
	public function sum();
}