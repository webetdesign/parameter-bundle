<?php

namespace WebEtDesign\ParameterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="WebEtDesign\ParameterBundle\Doctrine\ParameterRepository")
 * @ORM\Table(name="parameter")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="rarely_changes")
 */
class Parameter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=50, name="code")
     */
    private string $code = '';

    /**
     * @ORM\Column(type="string", length=10, nullable=false, name="type", options={"default": "text"})
     */
    private string $type = 'text';

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $config = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, name="label")
     */
    private string $label = '';

    /**
     * @ORM\Column(type="text", nullable=true, name="value")
     */
    private ?string $value = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, name="deletable", options={"default": 1})
     */
    private ?bool $deletable = true;

    private $file = null;

    /**
     * Parameter constructor.
     *
     * @param string|null $code
     */
    public function __construct($code = null)
    {
        if ($code) {
            $this->code = $code;
        }
    }

    public function __toString()
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getConfig(): ?string
    {
        return $this->config;
    }

    public function setConfig(?string $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getValue()
    {
        return $this->type === 'boolean' ? (bool) $this->value : $this->value;
    }

    public function setValue($value): self
    {
        $this->value = $this->type === 'boolean' ? intval($value) : $value;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?File $value): self
    {
        $this->file = $value;

        return $this;
    }

    public function getDeletable(): ?bool
    {
        return $this->deletable;
    }

    public function setDeletable(?bool $deletable): self
    {
        $this->deletable = $deletable;

        return $this;
    }
}
