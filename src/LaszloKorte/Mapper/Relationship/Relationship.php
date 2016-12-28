<?php

namespace LaszloKorte\Mapper\Relationship;

interface Relationship {
	public function getTargetType();

	public function getSourceType();

	public function getSourceKeys();

	public function getTargetKeys();

	public function getName();
}