<?php

namespace LaszloKorte\Graph\Path;

interface Path {
	public function length();

	public function relativeTo(TablePath $p);
}