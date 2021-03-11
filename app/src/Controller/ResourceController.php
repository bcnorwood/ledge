<?php

namespace App\Controller;

use App\Exception\EntityIdException;
use App\Exception\EntitySchemaException;
use App\Exception\JsonObjectException;
use App\Response\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use JsonSchema\Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

abstract class ResourceController extends AbstractController {
	protected $response;
	private $validator;
	private $serializer;
	protected $entityManager;

	protected $repository;

	// the name of the model this resource represents
	protected $entityName;

	// optionally, the name of the model whose repository will be accessed (defaults to same as above)
	protected $repoEntityName;

	public function __construct(ResponseFactory $responseFactory, Validator $validator, EntityManagerInterface $entityManager, SerializerInterface $serializer) {
		$this->response = $responseFactory;
		$this->validator = $validator;
		$this->serializer = $serializer;
		$this->entityManager = $entityManager;

		$this->entityName = self::getEntityName(static::ENTITY);

		if (defined(static::class . '::REPO_ENTITY')) {
			$this->repository = $entityManager->getRepository(static::REPO_ENTITY);
			$this->repoEntityName = self::getEntityName(static::REPO_ENTITY);
		} else {
			$this->repository = $entityManager->getRepository(static::ENTITY);
			$this->repoEntityName = $this->entityName;
		}
	}

    protected function find(int $id): object {
        if ($entity = $this->repository->find($id)) {
            return $entity;
        }

        throw new EntityIdException($this->repoEntityName, $id);
    }


    protected function delete(int $id): void {
    	$entity = $this->find($id);
    	$this->entityManager->remove($entity);
    }

    protected function save(object $entity = null): void {
    	if ($entity) {
    		$this->entityManager->persist($entity);
    	}

    	$this->entityManager->flush();
    }

	protected function hydrate(string $json, object $existing = null, $patch = false): object {
		try {
			$entity = json_decode($json, flags: JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);
		} catch (JsonException $exception) {
			throw new JsonObjectException($exception->getMessage());
		}

		$schema = static::ENTITY::JSON_SCHEMA;

		// if this is a PATCH request, remove all required parameters as any property omitted is preserved
		if ($patch) {
			unset($schema['required']);
		}

		if ($this->validator->validate($entity, $schema)) {
			throw new EntitySchemaException($this->entityName, $this->validator->getErrors());
		}

		return $this->serializer->denormalize(
			$entity,
			static::ENTITY,
			context: [AbstractNormalizer::OBJECT_TO_POPULATE => $existing]
		);
	}

	private static function getEntityName(string $class) {
		// get the class's short (non-namespaced) name, split TitleCased/CamelCased words, and format
		return strtolower(implode(' ', preg_split('/(?<!^)(?=[A-Z])/', (new \ReflectionClass($class))->getShortName())));
	}
}
