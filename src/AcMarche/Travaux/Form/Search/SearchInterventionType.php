<?php

namespace AcMarche\Travaux\Form\Search;

use AcMarche\Travaux\Repository\BatimentRepository;
use AcMarche\Travaux\Repository\CategorieRepository;
use AcMarche\Travaux\Repository\DomaineRepository;
use AcMarche\Travaux\Repository\EtatRepository;
use AcMarche\Travaux\Repository\PrioriteRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchInterventionType extends AbstractType
{
    /**
     * @var BatimentRepository
     */
    private $batimentRepository;

    /**
     * @var DomaineRepository
     */
    private $domaineRepository;
    /**
     * @var EtatRepository
     */
    private $etatRepository;
    /**
     * @var PrioriteRepository
     */
    private $prioriteRepository;
    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    public function __construct(
        BatimentRepository $batimentRepository,
        CategorieRepository $categorieRepository,
        DomaineRepository $domaineRepository,
        EtatRepository $etatRepository,
        PrioriteRepository $prioriteRepository
    ) {
        $this->batimentRepository = $batimentRepository;
        $this->domaineRepository = $domaineRepository;
        $this->etatRepository = $etatRepository;
        $this->prioriteRepository = $prioriteRepository;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $batiments = $this->batimentRepository->getForSearch();
        $categories = $this->categorieRepository->getForSearch();
        $domaines = $this->domaineRepository->getForSearch();
        $etats = $this->etatRepository->getForSearch();
        $priorites = $this->prioriteRepository->getForSearch();
        $affecte_prive = ['Oui' => 1, 'Non' => 0];

        $sorts = array(
            'Numéro' => 'id',
            'Intitule' => 'intitule',
            'Priorité' => 'priorite',
            'Date' => 'date_introduction',
        );

        $builder
            ->add(
                'intitule',
                SearchType::class,
                array(
                    'required' => false,
                    'attr' => array(
                        'placeholder' => 'Mot clef',
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
                'etat',
                ChoiceType::class,
                array(
                    'choices' => $etats,
                    'required' => false,
                    'placeholder' => 'Choisissez un état',
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
                'priorite',
                ChoiceType::class,
                array(
                    'choices' => $priorites,
                    'required' => false,
                    'placeholder' => 'Choisissez une priorité',
                )
            )
            ->add(
                'affecte_prive',
                ChoiceType::class,
                array(
                    'choices' => $affecte_prive,
                    'required' => false,
                    'placeholder' => 'Attribué à un privé',
                )
            )
            ->add(
                'batiment',
                ChoiceType::class,
                array(
                    'choices' => $batiments,
                    'required' => false,
                    'placeholder' => 'Choisissez un bâtiment',
                )
            )
            ->add(
                'categorie',
                ChoiceType::class,
                array(
                    'choices' => $categories,
                    'required' => false,
                    'placeholder' => 'Choisissez une catégorie',
                )
            )
            ->add(
                'domaine',
                ChoiceType::class,
                array(
                    'choices' => $domaines,
                    'required' => false,
                    'placeholder' => 'Choisissez un type',
                )
            )
            ->add(
                'sort',
                ChoiceType::class,
                array(
                    'choices' => $sorts,
                    'required' => false,
                    'placeholder' => 'Trier par',
                )
            )
            ->add(
                'submit',
                SubmitType::class,
                array(
                    'label' => 'Rechercher',
                )
            )
            ->add(
                'raz',
                SubmitType::class,
                array(
                    'label' => 'Raz',
                    'attr' => array(
                        'class' => 'btn-sm btn-info',
                        'title' => 'Réinitialiser la recherche',
                    ),
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array());

    }
}
