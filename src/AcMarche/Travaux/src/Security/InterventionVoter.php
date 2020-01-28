<?php

namespace AcMarche\Travaux\Security;

use AcMarche\Travaux\Entity\Intervention;
use AcMarche\Travaux\Entity\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
class InterventionVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"

    const ADD_SUIVI = 'add_suivi';
    const SHOW = 'show';
    const EDIT = 'edit';
    const DELETE = 'delete';
    protected $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Intervention && in_array(
                $attribute,
                [self::ADD_SUIVI, self::SHOW, self::EDIT, self::DELETE],
                true
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $intervention, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if ($user->hasRole('ROLE_TRAVAUX_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canView($intervention, $token);
            case self::ADD_SUIVI:
                return $this->canAddSuivi($intervention, $token);
            case self::EDIT:
                return $this->canEdit($intervention, $token);
            case self::DELETE:
                return $this->canDelete($intervention, $token);
        }

        return false;
    }

    private function canView(Intervention $intervention, TokenInterface $token)
    {
        if ($this->canEdit($intervention, $token)) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_REDACTEUR'])) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_AUTEUR'])) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_CONTRIBUTEUR'])) {
            return $this->checkOwner($intervention, $token);
        }

        return false;
    }

    private function canEdit(Intervention $intervention, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_REDACTEUR'])) {
            return true;
        }

        return $this->checkOwner($intervention, $token);
    }

    private function canAddSuivi(Intervention $intervention, TokenInterface $token)
    {
        return true;
    }

    private function canDelete(Intervention $intervention, TokenInterface $token)
    {
        if ($this->canEdit($intervention, $token)) {
            return true;
        }

        return false;
    }

    private function checkOwner(Intervention $intervention, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($intervention->getUserAdd() == $user->getUsername()) {
            return true;
        }

        return false;
    }
}
