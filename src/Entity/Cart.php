<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Cart
{
    const MAX_PRODUCTS = 3;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $created;

    /**
     * @ORM\Column(type="date")
     */
    private $modified;

    /**
     * @ORM\ManyToMany(targetEntity="Product", orphanRemoval=true)
     * @ORM\JoinTable(name="carts_products",
     *        joinColumns={@ORM\JoinColumn(name="cart_id", referencedColumnName="id", unique=false, onDelete="CASCADE")},
     *        inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id", unique=false, onDelete="CASCADE")}
     *    )
     * @var \Doctrine\Common\Collections\Collection|Product[] $products
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getModified(): ?\DateTimeInterface
    {
        return $this->modified;
    }

    public function setModified(\DateTimeInterface $modified): self
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product[] $products
     */
    public function setProducts(array $products): self
    {
        $this->products = $products;
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

    /**
     * @return float
     */
    public function getTotalProducts(): float
    {
        $total = 0;

        foreach ($this->getProducts() as $product) {
            $total += $product->getPrice();
        }

        return $total;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            return $this;
        }

        $this->products->add($product);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeProduct(Product $removedProduct): self
    {
        if (!$this->products->contains($removedProduct)) {
            return $this;
        }

        $this->products->removeElement($removedProduct);

        return $this;
    }
}
