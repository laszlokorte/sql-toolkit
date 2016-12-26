<?php

namespace LaszloKorte\Mapper\Query\Condition;

use LaszloKorte\Mapper\Query\Condition\Operator;

trait OperatorTrait {
	public function _and($other) {
		return new Operator\Conjunction($this, $other);
	}

	public function _or($other) {
		return new Operator\Disjunction($this, $other);

	}

	public function _not() {
		return new Operator\Negation($this);
	}

	public function __call($method, $args) {
		if(in_array($method, ['and', 'or', 'not'])) {
			return call_user_func_array(array($this, '_'.$method), $args);
		} elseif(is_callable(['parent', '__call'])) {
			return parent::__call($method, $args);
		} else {
			throw new \Exception('Call to undefined method '.get_class($this).'::'.$method.'()');
		}
	}
}
