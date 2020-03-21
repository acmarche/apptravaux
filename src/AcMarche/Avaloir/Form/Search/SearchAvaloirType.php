<?php

namespace AcMarche\Avaloir\Form\Search;

use AcMarche\Avaloir\Data\Localite;
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
    /**
     * @var Localite
     */
    private $localite;

    public function __construct(
        VillageRepository $villageRepository,
        QuartierRepository $quartierRepository,
        Localite $localite
    ) {
        $this->villageRepository = $villageRepository;
        $this->quartierRepository = $quartierRepository;
        $this->localite = $localite;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $villages = $this->villageRepository->getForSearch();
        $quartiers = $this->quartierRepository->getForSearch();
        $rues = $this->localite->getListRues();
        $rues = array_combine($rues, $rues);

        $builder
            ->add(
                'rue',
                ChoiceType::class,
                array(
                    'choices' => $rues,
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
                'quartier',
                ChoiceType::class,
                array(
                    'choices' => $quartiers,
                    'required' => false,
                    'placeholder' => 'Choisissez un quartier',
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
            )
            ->add(
                'raz',
                SubmitType::class,
                [
                    'attr' => ['class' => ' mr-1 btn-primary ', 'title' => 'Réinitialiser la recherche'],
                ]
            );
    }
}
