<?php

namespace App\Services\LoggerService;

use App\Entity\AuthUser;
use App\Entity\Logger\Log;
use App\Entity\Logger\LogAction;
use App\Services\InfoService\AuthUserInfoService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class LogService
 * @package App\Services\LoggerService
 */
class LogService
{
    const
        DEFAULT_USER_LOGIN_DESCRIPTION = 'User successfully logged in.',
        DEFAULT_USER_LOGOUT_DESCRIPTION = 'User successfully logged out.';

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
     * @param DateTime $createdAt
     * @return bool
     */
    private function createLog(
        string $user,
        ?LogAction $logAction,
        string $description,
        DateTime $createdAt
    ): bool {
        $log = new Log();
        $log->setUserString($user);
        $log->setAction($logAction);
        $log->setDescription($description);
        $log->setCreatedAt($createdAt);
        try {
            $this->em->persist($log);
            $this->em->flush();
        } catch (Exception $e) {
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
                'enabled' => true
            ]),
            $this->description ?? self::DEFAULT_USER_LOGOUT_DESCRIPTION,
            new DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logLoginEvent(): bool
    {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'login/logout',
                'enabled' => true
            ]),
            $this->description ?? self::DEFAULT_USER_LOGIN_DESCRIPTION,
            new DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logCreateEvent(): bool {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'create',
                'enabled' => true
            ]),
            $this->description,
            new DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logUpdateEvent(): bool {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'update',
                'enabled' => true
            ]),
            $this->description,
            new DateTime('now')
        );
    }

    /**
     * @return bool
     */
    public function logDeleteEvent(): bool {
        return $this->createLog(
            $this->user,
            $this->em->getRepository(LogAction::class)->findOneBy([
                'name' => 'delete',
                'enabled' => true
            ]),
            $this->description,
            new DateTime('now')
        );
    }

    /**
     * @param $user
     * @return LogService
     */
    public function setUser(AuthUser $user) {

        $this->user =
            'id: '
            . $user->getId()
            . ' User: '
            . (new AuthUserInfoService())->getFIO($user)
            . ' Phone: '
            .  $user->getPhone();
        return $this;
    }

    /**
     * @param $error
     * @return LogService
     */
    public function setError($error) {
        $this->error = $error;
        return $this;
    }

    /**
     * @return LogService
     */
    public function getError() {
        return $this->error;
    }

    /**
     * @param $description
     * @return LogService
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