<?php

namespace AcMarche\Travaux\Form;

use AcMarche\Travaux\Entity\Batiment;
use AcMarche\Travaux\Entity\Categorie;
use AcMarche\Travaux\Entity\Domaine;
use AcMarche\Travaux\Entity\Etat;
use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Priorite;
use AcMarche\Travaux\Entity\Service;
use AcMarche\Travaux\Repository\CategorieRepository;
use AcMarche\Travaux\Repository\EtatRepository;
use AcMarche\Travaux\Repository\PrioriteRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class InterventionType extends AbstractType
{
    /**
     * @var AuthorizationChecker
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
        $admin = $this->authorizationChecker->isGranted('ROLE_TRAVAUX_ADMIN');

        $builder
            ->add('intitule')
            ->add(
                'domaine',
                EntityType::class,
                array(
                    'class' => Domaine::class,
                    'label' => 'Type',
                    'required'=>false,
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                )
            )
            ->add(
                'service',
                EntityType::class,
                [
                    'class' => Service::class,'required'=>false,
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'batiment',
                EntityType::class,
                [
                    'class' => Batiment::class,'required'=>false,
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'transmis',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' => 'Transmis ?',
                    'attr' => array(),
                )
            )
            ->add(
                'affectePrive',
                CheckboxType::class,
                array(
                    'required' => false,
                    'label' => 'Affecté à un privé',
                )
            )
            ->add(
                'date_rappel',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Date de rappel',
                    'required' => false,
                    'attr' => array( 'autocomplete' => 'off'),
                )
            )
            ->add(
                'descriptif',
                TextareaType::class,
                array(
                    'required' => true,
                    'attr' => array('rows' => 5),
                )
            )
            ->add('affectation')
            ->add(
                'soumis_le',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Soumis le',
                    'required' => false,
                    'attr' => array( 'autocomplete' => 'off'),
                )
            )
            ->add(
                'solution',
                TextareaType::class,
                array(
                    'required' => false,
                    'attr' => array('rows' => 5),
                )
            )
            ->add(
                'date_solution',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Date de solution',
                    'required' => false,
                    'attr' => array( 'autocomplete' => 'off'),
                )
            )
            ->add(
                'cout_main',
                MoneyType::class,
                array(
                    'required' => false,
                    'label' => 'Coût main d\'oeuvre',
                    'help' => 'Uniquement les chiffres',
                )
            )
            ->add(
                'cout_materiel',
                MoneyType::class,
                array(
                    'required' => false,
                    'label' => 'Coût matériel',
                    'help' => 'Uniquement les chiffres',
                )
            )
            ->add(
                'date_execution',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'A réaliser à partir du',
                    'required' => false,
                    'attr' => array( 'autocomplete' => 'off'),
                )
            );

        if ($admin) {
            $builder
                ->add(
                    'etat',
                    EntityType::class,
                    array(
                        'class' => Etat::class,
                        'required' => true,
                        'query_builder' => function (EtatRepository $er) {
                            return $er->getForList();
                        },
                        'attr' => ['class' => 'custom-select my-1 mr-sm-2'],

                    )
                )
                ->add(
                    'categorie',
                    EntityType::class,
                    array(
                        'class' => Categorie::class,
                        'required' => true,
                        'multiple' => false,
                        'query_builder' => function (CategorieRepository $er) {
                            return $er->getForList();
                        },
                        'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                    )
                )
                ->add(
                    'priorite',
                    EntityType::class,
                    array(
                        'class' => Priorite::class,
                        'required' => true,
                        'query_builder' => function (PrioriteRepository $er) {
                            return $er->getForList();
                        },
                        'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                    )
                );
        } else {
            $builder
                ->add(
                    'etat',
                    EntityType::class,
                    array(
                        'class' => Etat::class,
                        'required' => true,
                        'multiple' => false,
                        'query_builder' => function (EtatRepository $er) {
                            return $er->getForListDefault();
                        },
                        'attr' => ['class' => 'custom-select my-1 mr-sm-2'],

                    )
                )
                ->add(
                    'priorite',
                    EntityType::class,
                    array(
                        'class' => Priorite::class,
                        'required' => true,
                        'multiple' => false,
                        'query_builder' => function (PrioriteRepository $er) {
                            return $er->getForListDefault();
                        },
                        'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                    )
                )
                ->add(
                    'categorie',
                    EntityType::class,
                    array(
                        'class' => Categorie::class,
                        'required' => true,
                        'multiple' => false,
                        'query_builder' => function (CategorieRepository $er) {
                            return $er->getForListDefault();
                        },
                        'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
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
