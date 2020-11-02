<?php


namespace App\Controller\Logger;

use App\Entity\AuthUser;
use App\Entity\Logger\Log;
use App\Entity\Logger\LogAction;
use App\Services\InfoService\AuthUserInfoService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;


class Logger
{
    const
        DEFAULT_USER_LOGIN_DESCRIPTION = 'User successfully logged in.',
        DEFAULT_USER_LOGOUT_DESCRIPTION = 'User successfully logged out.'
    ;


    /**
     * @var LogAction
     */
    private $action;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $description;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var
     */
    private $error;

    /**
     * @var AuthUser
     */
    private $user;

    /**
     * Logger constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $user
     * @param LogAction|null $logAction
     * @param string $description
     * @param \DateTime $createdAt
     * @return bool
     */
    private function createLog(
        string $user,
        ?LogAction $logAction,
        string $description,
        \DateTime $createdAt
    ): bool {
        $log = new Log();

        $log->setUserString($user);
        $log->setAction($logAction);
        $log->setDescription($description);
        $log->setCreatedAt($createdAt);

        try {
            $this->em->persist($log);
            $this->em->flush();
        } catch (\Exception $e) {
            $this->setError($e);
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function logLogoutEvent(): bool
    {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'login/logout',
                'isActive' => true
            ]),
            $this->description ?? self::DEFAULT_USER_LOGOUT_DESCRIPTION,
            new \DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logLoginEvent() {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'login/logout',
                'isActive' => true
            ]),
            $this->description ?? self::DEFAULT_USER_LOGIN_DESCRIPTION,
            new \DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logCreateEvent() {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'create',
                'isActive' => true
            ]),
            $this->description,
            new \DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logUpdateEvent() {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'update',
                'isActive' => true
            ]),
            $this->description,
            new \DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logDeleteEvent() {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'delete',
                'isActive' => true
            ]),
            $this->description,
            new \DateTime('now')
        );
    }

    /**
     * @param $user
     * @return Logger
     */
    public function setUser(AuthUser $user) {

        $this->user = 'id: '. $user->getId() . ' User: '. (new AuthUserInfoService())->getFIO($user). ' Phone: '.  $user->getPhone();

        return $this;
    }

    /**
     * @param $error
     * @return Logger
     */
    public function setError($error) {
        $this->error = $error;
        return $this;
    }

    /**
     * @return Logger
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @param $description
     * @return Logger
     */
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function setCritical() {
        // TODO: telegram or email notifications?
        return true;
    }

}