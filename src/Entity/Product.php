<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use \Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     */
    private $created;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull()
     */
    private $modified;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param $created
     * @return $this
     */
    public function setCreated($created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param $modified
     * @return $this
     */
    public function setModified($modified): self
    {
        $this->modified = $modified;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     */
    public function beforeSave() {
        $now = new \DateTime();

        if (!$this->getId()) {
            $this->setCreated($now);
        }

        $this->setModified($now);
    }
}
