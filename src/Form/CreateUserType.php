<?php

namespace Vinorcola\PrivateUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vinorcola\PrivateUserBundle\Data\CreateUser;
use Vinorcola\PrivateUserBundle\Model\Config;

class CreateUserType extends AbstractType
{
    /**
     * CreateUserType constructor.
     *
     * @param Config $config
     */
    public function __construct(private Config $config) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices'      => $this->config->getUserTypes(),
                'choice_label' => function ($value) {
                    return 'private_user.userType.' . $value;
                },
            ])
            ->add('emailAddress', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class);
            // ->add('sendInvitation', CheckboxType::class, [
            //     'required' => false,
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', CreateUser::class);
        $resolver->setDefault('label_format', 'private_user.user.%name%');
    }
}
