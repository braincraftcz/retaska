<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    const BASKET_SESSION_NAME = 'basket';

    /**
     * @Route("/basket", name="basket")
     */
    public function index(SessionInterface $session, ProductRepository $repository)
    {
        $basket = $session->get(self::BASKET_SESSION_NAME);

        foreach ($basket as $id => $item) {
            $product = $repository->find($id);
            $basket[$id]['price'] = $product->getPrice();
        }

        return $this->render('basket/index.html.twig', [
            'basket' => $basket
        ]);
    }

    /**
     * @Route("/add-product/{id}", name="add_product")
     */
    public function addProduct(Product $product, SessionInterface $session)
    {
        $basket = $session->get(self::BASKET_SESSION_NAME);
//        [
//            1 => ['name' => 'Papírová taška', 'amount' => 1],
//            3 => ['name' => 'Látková taška', 'amount' => 1]
//        ]

        if (isset($basket[$product->getId()])) {
            $basket[$product->getId()]['amount']++;
        } else {
            $basket[$product->getId()] = ['name' => $product->getName(), 'amount' => 1];
        }

        $session->set(self::BASKET_SESSION_NAME, $basket);

        return $this->redirectToRoute('basket');
    }

    /**
     * @Route("/remove-product/{id}", name="remove_product")
     */
    public function removeProduct($id, SessionInterface $session)
    {
        $basket = $session->get(self::BASKET_SESSION_NAME);
//        [
//            1 => ['name' => 'Papírová taška', 'amount' => 1],
//            3 => ['name' => 'Látková taška', 'amount' => 1]
//        ]

        unset($basket[$id]);

        $session->set(self::BASKET_SESSION_NAME, $basket);

        return $this->redirectToRoute('basket');
    }
}
