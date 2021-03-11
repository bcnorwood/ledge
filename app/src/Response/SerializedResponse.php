<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

abstract class SerializedResponse extends JsonResponse {
	abstract protected function handle($data, array $options);

	public function __construct(SerializerInterface $serializer, array $arguments) {
		// first argument is result data - entity, collection, or error to return
		$result = $this->handle(array_shift($arguments), $arguments);

		// for convenience, allow returning data directly if not changing status/headers
		if (!isset($result['data'])) {
			$result = ['data' => $result];
		}

		// encode using Symfony's Serializer and flag data as JSON string
		$result['data'] = $serializer->serialize($result['data'], 'json');
		$result['json'] = true;

		parent::__construct(...$result);
	}
}
