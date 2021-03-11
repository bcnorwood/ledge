<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity
 */
final class UserSetting {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
            'value' => ['type' => 'string']
        ],
        'required' => ['name', 'value']
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="settings")
     * @ORM\JoinColumn(nullable=false)
     * @Ignore
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"json_schema"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"json_schema"})
     */
    private $value;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Gedmo\Timestampable(on="create")
     */
    private $created;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    public function getId(): ?int {
        return $this->id;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function setUser(User $user = null): self {
        $this->user = $user;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getValue(): ?string {
        return $this->value;
    }

    public function setValue(string $value): self {
        $this->value = $value;
        return $this;
    }

    public function getCreated(): ?DateTimeImmutable {
        return $this->created;
    }

    public function getUpdated(): ?DateTimeImmutable {
        return $this->updated;
    }
}
