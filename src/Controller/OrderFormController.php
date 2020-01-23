<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderFormType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order-form")
 */
class OrderFormController extends AbstractController
{
    /**
     * @Route("/confirm", name="order_form_confirm")
     */
    public function confirm(): Response
    {
        return $this->render('order_form/confirmation.html.twig');
    }

    /**
     * @Route("/", name="order_form_index", methods="GET|POST")
     */
    public function index(Request $request, SessionInterface $session, ProductRepository $repo): Response
    {
        $order = new Order;

        $basket = $session->get(BasketController::BASKET_SESSION_NAME);

        $totalPrice = 0;

        foreach ($basket as $id => $item) {
            $product = $repo->find($id);
            $order->addProduct($product);
            $totalPrice += $product->getPrice();
        }

        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $totalPrice += $order->getDelivery()->getPrice();
            $order->setTotalPrice($totalPrice);

            $order->setCreated(new \DateTime);

            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_form_confirm');
        }

        return $this->render('order_form/index.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
            'totalPrice' => $totalPrice
        ]);
    }
}
