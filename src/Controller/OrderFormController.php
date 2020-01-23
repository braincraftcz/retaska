<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/{id}", name="order_form_index", methods="GET|POST")
     */
    public function index(Request $request, Product $product): Response
    {
        $order = new Order;
        $order->setProduct($product);

        $form = $this->createForm(OrderFormType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->updateTotalPrice();

            $itemsOnStock = $product->getStock();

            if (($itemsOnStock - 1) < 0) {
                return $this->render('order_form/index.html.twig', [
                    'order' => $order,
                    'form' => $form->createView(),
                    'remainingOnStock' => $product->getStock()
                ]);
            }

            $order->setCreated(new \DateTime);
            $product->setStock($itemsOnStock - 1);

            $this->getDoctrine()->getManager()->persist($order);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_form_confirm');
        }

        return $this->render('order_form/index.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}
