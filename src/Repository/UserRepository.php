<?php

namespace App\Repository;

use App\Entity\AuthUser;
use App\Entity\Role;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function get_class;

/**
 * Class UserRepository
 * @method AuthUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthUser[]    findAll()
 * @method AuthUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class UserRepository extends AppRepository implements PasswordUpgraderInterface
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */
    private $passwordEncoder;

    /**
     * @var string Телефон системного пользователя
     * yaml:config/globals.yaml
     */
    private $SYSTEM_USER_PHONE;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder, string $systemUserPhone)
    {
        parent::__construct($registry, AuthUser::class);
        $this->passwordEncoder = $passwordEncoder;
        $this->SYSTEM_USER_PHONE = $systemUserPhone;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param UserInterface $user
     * @param string $newEncodedPassword
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof AuthUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * Добавление пользователя
     *
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     *
     * @return AuthUser|null
     * @throws ORMException
     */
    public function addUserFromFixtures(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool $enabled
    ): ?AuthUser
    {

        $user = (new AuthUser())
            ->setPhone($phone)
            ->setEnabled($enabled);
        $user
            ->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $password
                )
            )
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setRoles($role);
        $this->_em->persist($user);

        return $user;
    }

    /**
     * Возврачащет роли пользователя в виде коллекции объектов Role
     *
     * @param UserInterface $user
     *
     * @return int|mixed|string
     */
    public function getRoles(UserInterface $user)
    {
        $qb = $this->_em->getRepository(Role::class)
            ->createQueryBuilder('r');
        return $qb
            ->add('where', $qb->expr()->in('r.tech_name', $user->getRoles() ?? []))
            ->getQuery()
            ->getResult();
    }

    /**
     * @return AuthUser|null
     * @throws NonUniqueResultException
     */
    public function getSystemUser(): AuthUser
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.phone = :val')
            ->setParameter('val', $this->SYSTEM_USER_PHONE)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
