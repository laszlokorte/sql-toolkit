<?php

namespace LaszloKorte\Resource\Ordering;

final class OrderingController {

	private $graph;

	public function __construct($graph) {
		$this->graph = $graph;
	}

	public function getOrdering($entityId, $parameters) {
		$entity = $this->graph->entityById($entityId);
		if(isset($parameters['order']['field'])) {
			$fieldId = $entity->field($parameters['order']['field'])->id();
		} else {
			$fieldId = NULL;
		}

		$order = $parameters['order']['dir'] ?? 'asc';
		return new Ordering($fieldId, $order);
	}
}