<?php

namespace Vinorcola\PrivateUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vinorcola\PrivateUserBundle\Data\ChangePassword;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'error_bubbling'  => true,
                'first_options'   => [
                    'label' => 'private_user.user.password',
                ],
                'second_options'  => [
                    'label' => 'private_user.user.repeatPassword',
                ],
                'invalid_message' => 'private_user.passwords_not_matching',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ChangePassword::class);
    }
}
