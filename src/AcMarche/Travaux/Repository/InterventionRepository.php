<?php

namespace AcMarche\Travaux\Repository;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Security\Group;
use AcMarche\Travaux\Entity\Security\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Intervention|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervention|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervention[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterventionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervention::class);
    }

    /**
     * @return Intervention[]
     */
    public function findAll()
    {
        return $this->findBy(array(), array('intitule' => 'ASC'));
    }

    /**
     * @param $args
     * @return QueryBuilder
     */
    public function setCriteria($args)
    {
        $intitule = isset($args['intitule']) ? $args['intitule'] : null;
        $categorie = isset($args['categorie']) ? $args['categorie'] : 0;
        $domaine = isset($args['domaine']) ? $args['domaine'] : 0;
        $batiment = isset($args['batiment']) ? $args['batiment'] : 0;
        $etat = isset($args['etat']) ? $args['etat'] : null;
        $priorite = isset($args['priorite']) ? $args['priorite'] : null;
        $id = isset($args['id']) ? $args['id'] : 0;
        $numero = isset($args['numero']) ? $args['numero'] : 0;
        $date_introduction = isset($args['date_introduction']) ? $args['date_introduction'] : null;
        $date_debut = isset($args['date_debut']) ? $args['date_debut'] : null;
        $date_fin = isset($args['date_fin']) ? $args['date_fin'] : null;
        //1 => archive, 2 => les deux, pas definis pas d'archive
        $archive = isset($args['archive']) ? $args['archive'] : null;
        $withAValider = isset($args['withAValider']) ? $args['withAValider'] : false;
        $affectation = isset($args['affectation']) ? $args['affectation'] : 0;
        $place = isset($args['place']) ? $args['place'] : null;
        $sort = isset($args['sort']) ? $args['sort'] : null;
        $affecte_prive = isset($args['affecte_prive']) ? $args['affecte_prive'] : false;
        $date_execution = isset($args['date_execution']) ? $args['date_execution'] : false;

        $qb = $this->createQueryBuilder('intervention');
        $qb->leftJoin('intervention.categorie', 'categorie', 'WITH');
        $qb->leftJoin('intervention.batiment', 'batiment', 'WITH');
        $qb->leftJoin('intervention.domaine', 'domaine', 'WITH');
        $qb->leftJoin('intervention.documents', 'documents', 'WITH');
        $qb->addSelect('categorie', 'batiment', 'domaine', 'documents');

        if ($intitule) {
            $qb->andWhere(
                'intervention.intitule LIKE :mot OR intervention.descriptif LIKE :mot OR intervention.affectation LIKE :mot'
            )
                ->setParameter('mot', '%'.$intitule.'%');
        }

        if ($affectation) {
            $qb->andWhere('intervention.affectation LIKE :aff')
                ->setParameter('aff', '%'.$affectation.'%');
        }


        if ($archive) {
            if ($archive == 1) {
                $qb->andWhere("intervention.archive = 1 ");
            }
            //$archive == 2
            //pas de contrainte;
        } else {
            $qb->andWhere("intervention.archive = 0 ");
        }

        if ($etat) {
            $qb->andWhere('intervention.etat = :etat')
                ->setParameter('etat', $etat);
        }

        if ($date_introduction) {
            $date_introduction = $date_introduction->format('Y-m-d');
            $qb->andWhere('intervention.date_introduction = :date')
                ->setParameter('date', $date_introduction);
        }

        if ($date_debut) {
            $date_start = $date_debut->format('Y-m-d');

            if ($date_fin) {
                $date_end = $date_fin->format('Y-m-d');
            } else {
                $date_end = $date_start;
            }

            $qb->andWhere('intervention.date_introduction BETWEEN :date_start AND :date_end')
                ->setParameter('date_start', $date_start)
                ->setParameter('date_end', $date_end);
        }

        if ($batiment) {
            $qb->andWhere('batiment.id = :bat')
                ->setParameter('bat', $batiment);
        }

        if ($domaine) {
            $qb->andWhere('domaine.id = :dom')
                ->setParameter('dom', $domaine);
        }

        if ($categorie) {
            $qb->andWhere('categorie.id = :cat')
                ->setParameter('cat', $categorie);
        }

        if ($priorite) {
            $qb->andWhere('intervention.priorite = :priorite')
                ->setParameter('priorite', $priorite);
        }

        if ($id) {
            $qb->andWhere("intervention.id IN ('$id') OR intervention.old_id IN ('$id')");
        }

        if ($numero) {
            $qb->andWhere("intervention.id = :num OR intervention.old_id = :num")
                ->setParameter('num', $numero);
        }

        if ($affecte_prive) {
            $qb->andWhere("intervention.affecte_prive = :prive")
                ->setParameter('prive', 1);
        } else {
            $qb->andWhere("intervention.affecte_prive = :prive")
                ->setParameter('prive', 0);
        }

        if ($place) {
            if (!is_array($place)) {
                $qb->andWhere('intervention.currentPlace LIKE :place')
                    ->setParameter('place', '%'.$place.'%');
            } else {
                foreach ($place as $value) {
                    $qb->orWhere('intervention.currentPlace LIKE :arg')
                        ->setParameter('arg', '%'.$value.'%');
                }
            }
        } /**
         * intervention en attente de validation
         * non pour tous
         * sauf pour les contributeurs sinon ne voit rien
         */
        elseif (!$withAValider) {
            $qb->andWhere('intervention.currentPlace LIKE :place')
                ->setParameter('place', '%published%');
        }

        $today = new \DateTime('now');
        $qb->andWhere('intervention.date_execution <= :date OR intervention.date_execution IS NULL')
            ->setParameter('date', $today->format('Y-m-d'));

        if ($sort) {
            $qb->addOrderBy('intervention.'.$sort, 'DESC');
        } else {
            $qb->addOrderBy('intervention.priorite', 'DESC');
            $qb->addOrderBy('intervention.date_introduction', 'ASC');
        }

        return $qb;
    }

    /**
     * @param array $args
     * @return Intervention[]
     */
    public function search($args)
    {
        $user = isset($args['user']) ? $args['user'] : null;
        $role = isset($args['role']) ? $args['role'] : null;

        $qb = $this->setCriteria($args);

        if ($user && $role) {
            $this->setUserConstraint($user, $role, $qb);
        }

        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * Interventions à faire plus tard
     * @return Intervention[]
     */
    public function getInterventionsReportees()
    {
        $today = new \DateTime('now');
        $qb = $this->createQueryBuilder('intervention');

        $qb->andWhere('intervention.date_execution > :date ')
            ->setParameter('date', $today->format('Y-m-d'));

        $qb->andWhere("intervention.archive = 0 ");

        $query = $qb->getQuery();

        $results = $query->getResult();

        return $results;
    }

    /**
     * Retourne La liste des interventions à valider pour l'admin ou l'auteur
     * @param array $args
     * @return Intervention[]
     */
    public function getInterventionsToValid($args)
    {
        $qb = $this->createQueryBuilder('intervention');
        $qb->leftJoin('intervention.categorie', 'categorie', 'WITH');
        $qb->leftJoin('intervention.batiment', 'batiment', 'WITH');
        $qb->leftJoin('intervention.domaine', 'domaine', 'WITH');
        $qb->leftJoin('intervention.documents', 'documents', 'WITH');
        $qb->addSelect('categorie', 'batiment', 'domaine', 'documents');

        $qb->where("intervention.archive = 0 ");

        $places = isset($args['places']) ? $args['places'] : [];

        $string = '';
        foreach ($places as $place) {
            $string .= "intervention.currentPlace LIKE '%$place%' OR ";
        }
        $string = substr($string, 0, -3);
        $qb->andWhere($string);

        $query = $qb->getQuery();
        $results = $query->getResult();

        return $results;
    }

    public function setUserConstraint(User $user, $role, QueryBuilder $qb)
    {
        $em = $this->getEntityManager();
        $usernames = false;

        /**
         * vois toutes ses demandes et celles du groupe contributeur
         */
        if ($role == 'AUTEUR') {
            $group = $em->getRepository(Group::class)->findOneBy(
                array(
                    'name' => 'TRAVAUX_CONTRIBUTEUR',
                )
            );

            if ($group) {
                $usernames = [];
                $users = $group->getUsers();
                foreach ($users as $tuser) {
                    $usernames[] = $tuser->getUsername();
                }
                $usernames[] = $user->getUsername();
            }
            $qb->andWhere("intervention.currentPlace NOT LIKE '%auteur_checking%' ");
        }

        /**
         * ne peut voir que ses demandes
         */
        if ($role == 'CONTRIBUTEUR') {
            $usernames = $user->getUsername();
        }

        /**
         * voir les siennes
         * celles en attente de validation admin
         */
        if ($role == 'REDACTEUR') {
            $username = $user->getUsername();
            /**
             * WHERE ((`user_add` LIKE 'redacteur' AND `current_place` LIKE '%admin_checking%')
             * OR (`user_add` LIKE 'redacteur')
             * OR (`current_place` LIKE '%published%'))
             */

            $qb->andWhere(
                "(
            (intervention.user_add = :user AND intervention.currentPlace NOT LIKE '%admin_checking%') OR
            (intervention.user_add = :user) OR 
            (intervention.currentPlace LIKE '%published%')
            )"
            )
                ->setParameter('user', $username);
        }

        if (is_array($usernames) && count($usernames) > 0) {
            $string = implode("','", $usernames);
            $qb->andWhere("intervention.user_add IN ('$string')");
        } elseif ($usernames) {
            $qb->andWhere('intervention.user_add = :user')
                ->setParameter('user', $usernames);
        }

        return $qb;
    }
}
