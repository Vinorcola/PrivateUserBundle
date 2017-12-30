<?php

namespace Vinorcola\PrivateUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vinorcola\PrivateUserBundle\Data\CreateUser;

class CreateUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('emailAddress', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('sendInvitation', CheckboxType::class, [
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', CreateUser::class);
        $resolver->setDefault('label_format', 'private_user.user.%name%');
    }
}
