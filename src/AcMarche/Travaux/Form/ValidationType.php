<?php

namespace AcMarche\Travaux\Form;

use AcMarche\Travaux\Entity\Intervention;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ValidationType extends AbstractType
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'accepter',
                SubmitType::class,
                array(
                    'label' => 'Accepter',
                    'attr' => array('class' => 'btn-success mr-1'),
                )
            )
            ->add(
                'refuser',
                SubmitType::class,
                array(
                    'label' => 'Refuser',
                    'attr' => array('class' => 'btn-danger ml-1'),
                )
            )
            ->add(
                'message',
                TextareaType::class,
                array(
                    'required' => false,
                    'label' => 'Laissez un message',
                    'mapped' => false,
                    'attr' => array('cols' => 50, 'rows' => 5),
                )
            );
        if ($this->authorizationChecker->isGranted("ROLE_TRAVAUX_ADMIN")) {
            $builder->add(
                'plusinfo',
                SubmitType::class,
                array(
                    'label' => 'Plus d\'infos',
                    'attr' => array('class' => 'btn-warning'),
                )
            )
                ->add(
                    'reporter',
                    SubmitType::class,
                    array(
                        'label' => 'Reporter',
                        'attr' => array('class' => 'btn-primary'),
                    )
                )
                ->add(
                    'date_execution',
                    DateType::class,
                    array(
                        'widget' => 'single_text',
                        'label' => 'A réaliser à partir du',
                        'format' => 'dd/MM/yyyy',
                        'required' => false,
                        'help' => 'Si reporter, choisissez une date d\'exécution',
                        'attr' => array('class' => 'datepicker', 'autocomplete' => 'off'),
                    )
                );
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Intervention::class,
            )
        );
    }
}
