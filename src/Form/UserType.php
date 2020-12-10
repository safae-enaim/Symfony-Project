<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserType extends AbstractType
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $encoder = $this->encoder;
        $builder
            ->add('first_name', TextType::class, [
                'required' => false,
            ])
            ->add('last_name', TextType::class, [
                'required' => false,
            ])
            ->add('email',  EmailType::class, [])
            ->add('password', PasswordType::class, [])
            ->add('phone', TextType::class, [
                'required' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($encoder) {
                /** @var User */
                $user = $event->getData();
                $form = $event->getForm();
                if ($user)
                    $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
