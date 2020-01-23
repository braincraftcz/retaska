<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Delivery;
use App\Entity\Order;
use App\Entity\Payment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, ['label' => 'E-mail'])
            ->add('phone', null, ['label' => 'Telefon'])
            ->add('nameAndSurname', null, ['label' => 'Jméno a příjmení'])
            ->add('street', null, ['label' => 'Ulice a číslo popisné'])
            ->add('city', null, ['label' => 'Město'])
            ->add('zip', null, ['label' => 'PSČ'])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'label' => 'Země'
            ])
            ->add('note', null, ['label' => 'Poznámka'])
            ->add('payment', EntityType::class, [
                'class' => Payment::class,
                'choice_label' => 'name',
                'label' => 'Platba'
            ])
            ->add('delivery', EntityType::class, [
                'class' => Delivery::class,
                'choice_label' => 'name',
                'label' => 'Doprava'
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Odeslat objednávku',
                'attr' => ['class' => 'btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
