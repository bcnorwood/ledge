<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

final class EntityResponse extends SerializedResponse {
	protected function handle($entity, array $options) {
		$result = ['data' => $entity];

		if (isset($options['created'])) {
			$result += [
				'status' => Response::HTTP_CREATED,
				'headers' => ['location' => $options['created']]
			];
		}

		return $result;
	}
}
