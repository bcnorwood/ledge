<?php

namespace App\Response;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

final class ResponseFactory {
	private $serializer;

	public function __construct(SerializerInterface $serializer) {
		$this->serializer = $serializer;
	}

	public function __invoke(...$arguments) {
		// pass through to base response object
		return new Response(...$arguments);
	}

	public function __call(string $method, array $arguments) {
		// turn ResponseFactory::type into the class TypeResponse
		$class = sprintf('%s\\%s%s', __NAMESPACE__, ucfirst($method), 'Response');
		return new $class($this->serializer, $arguments);
	}
}
