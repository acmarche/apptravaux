<?php

namespace AcMarche\Travaux\Security;

use AcMarche\Travaux\Entity\Security\User;
use AcMarche\Travaux\Entity\Suivi;
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
class SuiviVoter extends Voter
{
    // Defining these constants is overkill for this simple application, but for real
    // applications, it's a recommended practice to avoid relying on "magic strings"

    const ADD = 'add';
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
        return $subject instanceof Suivi && in_array(
                $attribute,
                [self::ADD, self::SHOW, self::EDIT, self::DELETE],
                true
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $suivi, TokenInterface $token)
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
                return $this->canView($suivi, $token);
            case self::ADD:
                return $this->canAddSuivi($suivi, $token);
            case self::EDIT:
                return $this->canEdit($suivi, $token);
            case self::DELETE:
                return $this->canDelete($suivi, $token);
        }

        return false;
    }

    private function canView(Suivi $suivi, TokenInterface $token)
    {
        if ($this->canEdit($suivi, $token)) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_REDACTEUR'])) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_AUTEUR'])) {
            return true;
        }

        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_CONTRIBUTEUR'])) {
            return $this->checkOwner($suivi, $token);
        }

        return false;
    }

    private function canEdit(Suivi $suivi, TokenInterface $token)
    {
        return $this->checkOwner($suivi, $token);
    }

    private function canAdd(Suivi $suivi, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, ['ROLE_TRAVAUX_ADD'])) {
            return true;
        }

        return false;
    }

    private function canAddSuivi(Suivi $suivi, TokenInterface $token)
    {
        if ($this->canEdit($suivi, $token)) {
            return true;
        }

        if ($this->decisionManager->decide($token, [''])) {
            return $this->checkOwner($suivi, $token);
        }

        return false;
    }

    private function canDelete(Suivi $suivi, TokenInterface $token)
    {
        if ($this->canEdit($suivi, $token)) {
            return true;
        }

        return false;
    }

    private function checkOwner(Suivi $suivi, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($suivi->getUserAdd() == $user->getUsername()) {
            return true;
        }

        return false;
    }
}
