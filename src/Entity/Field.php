<?php

namespace App\Entity;

use App\Repository\FieldRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FieldRepository::class)
 */
class Field
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Entity::class, inversedBy="fields")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $length;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $default_value;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $validation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_nullable;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_on_form;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_on_index;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_on_show;

    /**
     * @ORM\ManyToOne(targetEntity=Entity::class)
     */
    private $relation_entity;

    /**
     * @ORM\ManyToOne(targetEntity=Field::class)
     */
    private $relation_field;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): self
    {
        $this->entity = $entity;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;

        return $this;
    }

    public function getDefaultValue(): ?string
    {
        return $this->default_value;
    }

    public function setDefaultValue(?string $default_value): self
    {
        $this->default_value = $default_value;

        return $this;
    }

    public function getValidation(): ?string
    {
        return $this->validation;
    }

    public function setValidation(?string $validation): self
    {
        $this->validation = $validation;

        return $this;
    }

    public function getIsNullable(): ?bool
    {
        return $this->is_nullable;
    }

    public function setIsNullable(bool $is_nullable): self
    {
        $this->is_nullable = $is_nullable;

        return $this;
    }

    public function getIsOnForm(): ?bool
    {
        return $this->is_on_form;
    }

    public function setIsOnForm(bool $is_on_form): self
    {
        $this->is_on_form = $is_on_form;

        return $this;
    }

    public function getIsOnIndex(): ?bool
    {
        return $this->is_on_index;
    }

    public function setIsOnIndex(bool $is_on_index): self
    {
        $this->is_on_index = $is_on_index;

        return $this;
    }

    public function getIsOnShow(): ?bool
    {
        return $this->is_on_show;
    }

    public function setIsOnShow(bool $is_on_show): self
    {
        $this->is_on_show = $is_on_show;

        return $this;
    }

    public function getRelationEntity(): ?Entity
    {
        return $this->relation_entity;
    }

    public function setRelationEntity(?Entity $relation_entity): self
    {
        $this->relation_entity = $relation_entity;

        return $this;
    }

    public function getRelationField(): ?self
    {
        return $this->relation_field;
    }

    public function setRelationField(?self $relation_field): self
    {
        $this->relation_field = $relation_field;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
