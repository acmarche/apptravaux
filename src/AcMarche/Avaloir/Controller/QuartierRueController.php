<?php

namespace AcMarche\Avaloir\Controller;

use AcMarche\Avaloir\Entity\Rue;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use AcMarche\Avaloir\Entity\Quartier;
use AcMarche\Avaloir\Form\QuartierRueType;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Intervention controller.
 *
 * @Route("/quartier/rue")
 * @IsGranted("ROLE_TRAVAUX_AVALOIR")
 */
class QuartierRueController extends AbstractController
{
    /**
     * Displays a form to create a new Suivis entity.
     *
     * @Route("/new/{slugname}", name="quartierrue_new", methods={"GET","POST"})
     *
     */
    public function new(Request $request, Quartier $quartier)
    {
        $em = $this->getDoctrine()->getManager();

        $ruesTmp = $em->getRepository(Rue::class)->getByQuartier($quartier);
        $ruesOld = array();
        foreach ($ruesTmp as $rue) {
            $ruesOld[] = $rue->getId();
        }

        $form = $this->createCreateForm($quartier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $rueIds = $data->getRueIds();

            $rues = $rueIds ? array_unique(explode("|", $rueIds)) : array();
            //donnees en int sinon comparaison faussee
            foreach ($rues as $key => $rue) {
                $rues[$key] = intval($rue);
            }

            $diff = count(array_diff($ruesOld, $rues));
            $diff_count = count($ruesOld) - count($rues);

            if ($diff === 0 and $diff_count === 0) {
                $this->addFlash("warning", "Aucun changement n'a été effectué");
                return $this->redirectToRoute('quartier_show', array('slugname' => $quartier->getSlugname()));
            }

            $enMoins = array_diff($ruesOld, $rues);
            $enPlus = array_diff($rues, $ruesOld);

            $this->setRues($quartier, $enPlus, 'add');
            $this->setRues($quartier, $enMoins, 'remove');

            $em->flush();

            $this->addFlash('success', 'Les rues ont bien modifiées.');
            return $this->redirectToRoute('quartier_show', array('slugname' => $quartier->getSlugname()));
        }

        /**
         * Rues deja associees
         */
        $rues = array();
        $i = 0;
        foreach ($ruesTmp as $rue) {
            $rues[$i]["value"] = $rue->getId();
            $rues[$i]["label"] = $rue->getNom();
            $i++;
        }

          return $this->render('avaloir/quartier_rue/new.html.twig',array(
            'entity' => $quartier,
            'rues' => json_encode($rues),
            'form' => $form->createView(),
        ));
    }

    /**
     * Associe ou desassocie une rue
     * @param Quartier $quartier
     * @param array $rues
     * @param string $action
     */
    protected function setRues($quartier, $rues, $action)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($rues as $rueId) {
            $rueId = (int)$rueId;
            if ($rueId) {
                $args = array('id' => $rueId);
                $rue = $em->getRepository(Rue::class)->findOneBy($args);
                if ($rue) {
                    if ($action === 'add') {
                        $rue->setQuartier($quartier);
                    } elseif ($action === 'remove') {
                        $rue->setQuartier(null);
                    }
                    $em->persist($rue);
                }
            }
        }
    }

    private function createCreateForm(Quartier $entity)
    {
        $form = $this->createForm(QuartierRueType::class, $entity, array(
            'action' => $this->generateUrl('quartierrue_new', array('slugname' => $entity->getSlugname())),
            'method' => 'POST',
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Update'));

        return $form;
    }
}
