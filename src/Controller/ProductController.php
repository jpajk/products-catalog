<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/products")
 */
class ProductController extends AbstractController
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
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $pageNumber = (int) $request->query->get('page_no');

        $maxPerPage = 3;

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator(
            $productRepository
                ->createQueryBuilder('q')
                ->orderBy('q.id', 'ASC')
                ->getQuery()
        );

        if (!$pageNumber || $pageNumber < 1) {
            $pageNumber = 1;
        }

        $paginator
            ->getQuery()
            ->setFirstResult($maxPerPage * ($pageNumber - 1))
            ->setMaxResults(3);

        return $this->json(
            $paginator->getIterator()->getArrayCopy()
        );
    }

    /**
     * @Route("/", name="product_new", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $product = $this->deserializeEntity($request);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json($product);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->json($product);
    }

    /**
     * @Route("/{id}", name="product_update", methods={"PATCH","PUT"})
     */
    public function update(Request $request, Product $existingProduct): Response
    {
        $product = $this->deserializeEntity($request);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist(
            $existingProduct
                ->setPrice($product->getPrice())
                ->setTitle($product->getTitle())
        );

        $entityManager->flush();

        return $this->json($existingProduct);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Product $product): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json($product);
    }

    /**
     * @param Request $request
     * @return Product
     */
    public function deserializeEntity(Request $request): Product
    {
        return $this->serializer->deserialize($request->getContent(), Product::class, 'json');
    }
}
