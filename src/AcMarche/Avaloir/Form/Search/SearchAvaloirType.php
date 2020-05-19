<?php

namespace AcMarche\Avaloir\Form\Search;

use AcMarche\Avaloir\Entity\Rue;
use AcMarche\Avaloir\Repository\RueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchAvaloirType extends AbstractType
{
    /**
     * @var RueRepository
     */
    private $rueRepository;

    public function __construct(RueRepository $rueRepository)
    {
        $this->rueRepository = $rueRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $villages = $this->rueRepository->getVillages();

        $builder
            ->add(
                'rue',
                EntityType::class,
                array(
                    'class' => Rue::class,
                    'query_builder'=> function(RueRepository $rueRepository) {
                     return   $rueRepository->getForList();
                    },
                    'group_by'=>'village',
                    'required' => false,
                    'placeholder' => 'Choisissez une rue',
                )
            )
            ->add(
                'village',
                ChoiceType::class,
                array(
                    'choices' => $villages,
                    'required' => false,
                    'placeholder' => 'Choisissez un village',
                )
            )
            ->add(
                'id',
                IntegerType::class,
                array(
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Numéro',
                    ),
                )
            )
            ->add(
                'date_debut',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Date de début',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Entre le',

                    ),
                )
            )
            ->add(
                'date_fin',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Date de fin',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Et le',
                    ),
                )
            );
    }
}
