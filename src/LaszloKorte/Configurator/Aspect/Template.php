<?php

namespace LaszloKorte\Configurator\Aspect;

interface Template {
	public function getString();

	public function setProcessedTemplate($tpl);
	
	public function getProcessedTemplate();
}