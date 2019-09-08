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
        if ($options['require_current_password']) {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'label' => 'private_user.user.currentPassword',
                ]);
        }

        $builder
            ->add('newPassword', RepeatedType::class, [
                'type'            => PasswordType::class,
                'error_bubbling'  => true,
                'first_options'   => [
                    'label' => 'private_user.user.newPassword',
                ],
                'second_options'  => [
                    'label' => 'private_user.user.repeatNewPassword',
                ],
                'invalid_message' => 'private_user.passwords_not_matching',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ChangePassword::class);
        $resolver->setDefault('require_current_password', false);
        $resolver->setAllowedTypes('require_current_password', 'bool');
    }
}
