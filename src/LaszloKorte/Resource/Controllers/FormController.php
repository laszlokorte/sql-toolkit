<?php

namespace LaszloKorte\Resource\Controllers;

use LaszloKorte\Resource\ParameterBag;

final class FormController {

	public function getParams() {
		return new ParameterBag();
	}
}