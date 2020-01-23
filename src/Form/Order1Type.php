<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Order1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('phone')
            ->add('nameAndSurname')
            ->add('street')
            ->add('city')
            ->add('zip')
            ->add('note')
            ->add('totalPrice')
            ->add('submitted')
            ->add('created')
            ->add('product')
            ->add('country')
            ->add('payment')
            ->add('delivery')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
