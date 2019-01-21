<?php
/**
 * Created by PhpStorm.
 * User: jfsenechal
 * Date: 25/10/16
 * Time: 16:50
 */

namespace AcMarche\Travaux\DataFixtures\ORM;

use AcMarche\Travaux\Entity\Security\Group;
use AcMarche\Travaux\Entity\Security\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoadUsers extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $group_admin = new Group("TRAVAUX_ADMIN");
        $group_admin->addRole("ROLE_TRAVAUX_ADMIN");
        $group_admin->addRole("ROLE_TRAVAUX");
        $group_admin->addRole("ROLE_TRAVAUX_ADD");
        $group_admin->addRole("ROLE_TRAVAUX_VALIDATION");
        $group_admin->addRole("ROLE_TRAVAUX_AVALOIR");
        $group_admin->addRole("ROLE_ADMIN");
        $manager->persist($group_admin);

        $group_auteur = new Group("TRAVAUX_AUTEUR");
        $group_auteur->addRole("ROLE_TRAVAUX_AUTEUR");
        $group_auteur->addRole("ROLE_TRAVAUX");
        $group_auteur->addRole("ROLE_TRAVAUX_ADD");
        $group_auteur->addRole("ROLE_TRAVAUX_VALIDATION");
        $manager->persist($group_auteur);

        $group_redacteur = new Group("TRAVAUX_REDACTEUR");
        $group_redacteur->addRole("ROLE_TRAVAUX_REDACTEUR");
        $group_redacteur->addRole("ROLE_TRAVAUX");
        $group_redacteur->addRole("ROLE_TRAVAUX_ADD");
        $manager->persist($group_redacteur);

        $group_lecteur = new Group("TRAVAUX_LECTEUR");
        $group_lecteur->addRole("ROLE_TRAVAUX_LECTEUR");
        $group_lecteur->addRole("ROLE_TRAVAUX");
        $manager->persist($group_lecteur);

        $group_contributeur = new Group("TRAVAUX_CONTRIBUTEUR");
        $group_contributeur->addRole("ROLE_TRAVAUX_CONTRIBUTEUR");
        $group_contributeur->addRole("ROLE_TRAVAUX");
        $group_contributeur->addRole("ROLE_TRAVAUX_ADD");
        $manager->persist($group_contributeur);

        $group_avaloir = new Group("TRAVAUX_AVALOIR");
        $group_avaloir->addRole("ROLE_TRAVAUX_AVALOIR");
        $manager->persist($group_avaloir);

        $admininistrator = new User();
        $admininistrator->setNom('Super');
        $admininistrator->setPrenom('Admin');
        $this->setUser($admininistrator, 'administrator', "administrator@marche.be");
        $admininistrator->addGroup($group_admin);
        $admininistrator->addGroup($group_avaloir);
        $manager->persist($admininistrator);

        $admin = new User();
        $admin->setNom('Admin');
        $admin->setPrenom('Vincent');
        $this->setUser($admin, 'admin', "admin@marche.be");
        $admin->addGroup($group_admin);
        $admin->addGroup($group_avaloir);
        $manager->persist($admin);

        $auteur = new User();
        $auteur->setNom('Auteur');
        $auteur->setPrenom('Isabelle');
        $this->setUser($auteur, 'auteur', "auteur@marche.be");
        $auteur->addGroup($group_auteur);
        $manager->persist($auteur);

        $redacteur = new User();
        $redacteur->setNom('redacteur');
        $redacteur->setPrenom('Bruno');
        $this->setUser($redacteur, 'redacteur', "redacteur@marche.be");
        $redacteur->addGroup($group_redacteur);
        $manager->persist($redacteur);

        $lecteur = new User();
        $lecteur->setNom('Lecteur');
        $lecteur->setPrenom('Nicolas');
        $this->setUser($lecteur, 'lecteur', "lecteur@marche.be");
        $lecteur->addGroup($group_lecteur);
        $manager->persist($lecteur);

        $contributeur = new User();
        $contributeur->setNom('Contributeur');
        $contributeur->setPrenom('Jean marie');
        $this->setUser($contributeur, 'contributeur', "contributeur@marche.be");
        $contributeur->addGroup($group_contributeur);
        $manager->persist($contributeur);

        $jfsenechal = new User();
        $jfsenechal->setNom('Admin');
        $jfsenechal->setPrenom('Jf');
        $this->setUser($jfsenechal, 'jfadmin', "jfadmin@marche.be");
        $jfsenechal->addGroup($group_admin);
        $manager->persist($jfsenechal);

        $manager->flush();
    }

    protected function setUser(User $user, $username, $email)
    {
        $user->setUsername($username);
        $user->setUsernameCanonical($username);
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'acmarche'));
        $user->setEnabled(1);
        $user->setEmail($email);
        $user->setEmailCanonical($email);
    }
}
