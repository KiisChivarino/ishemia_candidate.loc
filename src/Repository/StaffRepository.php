<?php

namespace App\Repository;

use App\Entity\AuthUser;
use App\Entity\Staff;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class StaffRepository
 * @method Staff|null find($id, $lockMode = null, $lockVersion = null)
 * @method Staff|null findOneBy(array $criteria, array $orderBy = null)
 * @method Staff[]    findAll()
 * @method Staff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class StaffRepository extends AppRepository implements PasswordUpgraderInterface
{
    /**
     * StaffRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Staff::class);
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
     * @param UserInterface $authUser
     * @return object|null
     */
    public function getStaff(UserInterface $authUser): ?Staff
    {
        return $this->findOneBy(['AuthUser' => $authUser]);
    }
}
