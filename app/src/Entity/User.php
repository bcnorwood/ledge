<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 */
final class User {
    public const JSON_SCHEMA = [
        'type' => 'object',
        'properties' => [
            'first_name' => ['type' => 'string'],
            'last_name'  => ['type' => 'string'],
            'email'      => ['type' => 'string', 'format' => 'email'],
            'active'     => ['type' => 'boolean'],
            'settings'   => ['type' => 'array', 'items' => UserSetting::JSON_SCHEMA]
        ],
        'required' => ['first_name', 'last_name', 'email']
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"json_schema"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"json_schema"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"json_schema"})
     */
    private $email;

   /**
    * @ORM\Column(type="boolean")
     * @Groups({"json_schema"})
    */
   private $active = true;

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

    /**
     * @ORM\OneToMany(targetEntity=UserSetting::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     * @Groups({"json_schema"})
     */
    private $settings;

    public function __construct() {
        $this->settings = new ArrayCollection;
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function isActive(): bool {
        return $this->active;
    }

    public function setActive(bool $active): self {
        $this->active = $active;
        return $this;
    }

    public function getCreated(): ?DateTimeImmutable {
        return $this->created;
    }

    public function getUpdated(): ?DateTimeImmutable {
        return $this->updated;
    }

    /**
     * @return Collection|UserSetting[]
     */
    public function getSettings(): Collection {
        return $this->settings;
    }

    public function addSetting(UserSetting $setting): self {
        if (!$this->settings->contains($setting)) {
            $this->settings[] = $setting;
            $setting->setUser($this);
        }

        return $this;
    }

    public function removeSetting(UserSetting $setting): self {
        if ($this->settings->removeElement($setting)) {
            // set the owning side to null (unless already changed)
            if ($setting->getUser() === $this) {
                $setting->setUser(null);
            }
        }

        return $this;
    }
}
