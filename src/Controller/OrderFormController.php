<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\CountryRepository;
use App\Repository\DeliveryRepository;
use App\Repository\PaymentRepository;
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
     * @Route("/create/{id}", name="order_form_create_order")
     */
    public function createOrder(
        DeliveryRepository $deliveryRepository,
        PaymentRepository $paymentRepository,
        CountryRepository $countryRepository,
        Product $product
    ): Response {
        $order = new Order();
        $order->setProduct($product);
        $order->updateTotalPrice();

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return $this->redirectToRoute('order_form_index', ['id' => $order->getId()]);
    }

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
    public function index(Request $request, Order $order): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->updateTotalPrice();

            if ($form->get('updateTotalPrice')->isClicked()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('order_form_index', ['id' => $order->getId()]);
            }

            $itemsOnStock = $order->getProduct()->getStock();

            if (($itemsOnStock - 1) < 0) {
                return $this->render('order_form/index.html.twig', [
                    'order' => $order,
                    'form' => $form->createView(),
                    'remainingOnStock' => $order->getProduct()->getStock()
                ]);
            }

            $order->setCreated(new \DateTime);
            $order->setSubmitted(true);
            $order->getProduct()->setStock($itemsOnStock - 1);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('order_form_confirm');
        }

        return $this->render('order_form/index.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
        ]);
    }
}
