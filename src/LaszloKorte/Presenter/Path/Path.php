<?php

namespace LaszloKorte\Presenter\Path;

interface Path {
	public function length();

	public function relativeTo(TablePath $p);
}