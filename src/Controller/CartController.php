<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;

    /**
     * ProductController constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/", name="cart_new", methods={"POST"})
     */
    public function new(Request $request): Response
    {
        $cart = $this->deserializeEntity($request);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->json($cart);
    }

    /**
     * @Route("/{id}", name="cart_show", methods={"GET"})
     */
    public function show(Cart $cart): Response
    {
        return $this->json($cart);
    }

    /**
     * @Route("/{cart}/add_product/{product}",
     *     requirements={"id" = "\d+", "product_id" = "\d+"},
     *     name="cart_add_product",
     *     methods={"POST"}
     * )
     */
    public function addProduct(Cart $cart, Product $product, EntityManagerInterface $em): Response
    {
        $cart->addProduct($product);

        if(count($cart->getProducts()) > Cart::MAX_PRODUCTS) {
            throw new \RuntimeException("The maximum products in the cart is 3");
        }

        $em->persist($cart);
        $em->flush();

        return $this->json($cart);
    }

    /**
     * @Route("/{id}/remove_product/{product_id}", name="cart_remove_product", methods={"POST"})
     */
    public function removeProduct(EntityManagerInterface $em, Cart $cart, Product $product): Response
    {
        $cart->removeProduct($product);
        $em->persist($cart);
        $em->flush();

        return $this->json($cart);
    }

    /**
     * @param Request $request
     * @return Cart
     */
    public function deserializeEntity(Request $request): Cart
    {
        return $this->serializer->deserialize($request->getContent(), Cart::class, 'json');
    }
}
