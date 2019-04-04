<?php

namespace AcMarche\Avaloir\Form\Search;

use AcMarche\Avaloir\Repository\QuartierRepository;
use AcMarche\Avaloir\Repository\VillageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchAvaloirType extends AbstractType
{

    /**
     * @var VillageRepository
     */
    private $villageRepository;
    /**
     * @var QuartierRepository
     */
    private $quartierRepository;

    public function __construct(VillageRepository $villageRepository, QuartierRepository $quartierRepository)
    {
        $this->villageRepository = $villageRepository;
        $this->quartierRepository = $quartierRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $villages = $this->villageRepository->getForSearch();
        $quartiers = $this->quartierRepository->getForSearch();

        $builder
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
                'quartier',
                ChoiceType::class,
                array(
                    'choices' => $quartiers,
                    'required' => false,
                    'placeholder' => 'Choisissez un quartier',
                )
            )
            ->add(
                'nom',
                SearchType::class,
                array(
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Rue',
                    ),
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
                    'label' => 'Date d\'introduction',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Entre le',
                        'class' => 'datepicker',
                    ),
                )
            )
            ->add(
                'date_fin',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'Date d\'introduction',
                    'format' => 'dd/MM/yyyy',
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Et le',
                        'class' => 'datepicker',
                    ),
                )
            )
            ->add(
                'raz',
                SubmitType::class,
                [
                    'attr' => ['class'=>' mr-1 btn-primary ','title'=>'Réinitialiser la recherche'],
                ]
            );
    }
}
