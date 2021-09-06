<?php

namespace App\Entity;

use App\Repository\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EntityRepository::class)
 */
class Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $singular_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $plural_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $namespace;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Field::class, mappedBy="entity")
     */
    private $fields;

    /**
     * @ORM\Column(type="boolean")
     */
    private $has_public_page;

    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSingularName(): ?string
    {
        return $this->singular_name;
    }

    public function setSingularName(string $singular_name): self
    {
        $this->singular_name = $singular_name;

        return $this;
    }

    public function getPluralName(): ?string
    {
        return $this->plural_name;
    }

    public function setPluralName(string $plural_name): self
    {
        $this->plural_name = $plural_name;

        return $this;
    }

    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Field[]
     */
    public function getFields(): Collection
    {
        return $this->fields;
    }

    public function addField(Field $field): self
    {
        if (!$this->fields->contains($field)) {
            $this->fields[] = $field;
            $field->setEntity($this);
        }

        return $this;
    }

    public function removeField(Field $field): self
    {
        if ($this->fields->removeElement($field)) {
            // set the owning side to null (unless already changed)
            if ($field->getEntity() === $this) {
                $field->setEntity(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->singular_name;
    }

    public function getHasPublicPage(): ?bool
    {
        return $this->has_public_page;
    }

    public function setHasPublicPage(bool $has_public_page): self
    {
        $this->has_public_page = $has_public_page;

        return $this;
    }
}
