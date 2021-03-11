<?php

namespace App\Response;

final class CollectionResponse extends SerializedResponse {
	// CollectionResponse uses the second $options parameter for metadata
	protected function handle($entities, array $meta) {
		return ['result' => $entities, 'length' => count($entities)] + $meta;
	}
}
