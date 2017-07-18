<?php

namespace LaszloKorte\Resource\CollectionView;

final class CollectionViewController {
	private $graph;

	public function __construct($graph) {
		$this->graph = $graph;
	}

	public function getView($entityId, $parameters, $collection) {
		$entity = $this->graph->entityById($entityId);
		$fields = array_filter(iterator_to_array($entity->fields()), function($field) {
			return $field->isVisibleInCollection() && !$field->isSecret();
		});

		return new TableView($fields, $collection);
	}
}