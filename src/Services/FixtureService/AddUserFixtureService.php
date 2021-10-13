<?php

namespace App\Services\FixtureService;

use App\Entity\AuthUser;
use App\Entity\City;
use App\Entity\Patient;
use App\Entity\Position;
use App\Entity\Staff;
use App\Repository\CityRepository;
use App\Repository\HospitalRepository;
use App\Repository\PositionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AddUserFixtureService
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    /** @var PasswordEncoderInterface $passwordEncoder */

    private $passwordEncoder;
    /**@var PositionRepository $positionRepository */

    private $positionRepository;
    /** @var HospitalRepository $hospitalRepository*/
    private $hospitalRepository;

    /** @var CityRepository $cityRepository*/
    private $cityRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param PositionRepository $positionRepository
     * @param HospitalRepository $hospitalRepository
     * @param CityRepository $cityRepository
     */
    public function __construct(
        EntityManagerInterface   $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        PositionRepository       $positionRepository,
        HospitalRepository $hospitalRepository,
        CityRepository $cityRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->positionRepository = $positionRepository;
        $this->hospitalRepository = $hospitalRepository;
        $this->cityRepository = $cityRepository;
    }

    /**
     * Add System
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     */
    public function addSystem(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled
    ): void
    {
        $this->addAuthUser(
            $phone,
            $firstName,
            $lastName,
            $role,
            $password,
            $enabled
        );
    }

    /**
     * Add Admin
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     */
    public function addAdmin(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled
    ): void
    {
        $this->addAuthUser(
            $phone,
            $firstName,
            $lastName,
            $role,
            $password,
            $enabled
        );
    }

    /**
     * add Patient
     */
    public function addPatient(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled,
        string $hospitalName,
        string $cityName,
        string $address,
        bool $smsInformation,
        bool $emailInformation,
        DateTime $dateBirthDate,
        DateTime $heartAttackDate
    ): void
    {
        $hospital = $this->hospitalRepository->findOneBy(['name' => $hospitalName]);
        /** @var City $city */
        $city = $this->cityRepository->findOneBy(['name' => $cityName]);

        $authUser = $this->addAuthUser(
            $phone,
            $firstName,
            $lastName,
            $role,
            $password,
            $enabled
        );

        $patient = (new Patient())
            ->setAuthUser($authUser)
            ->setHospital($hospital)
            ->setCity($city)
            ->setAddress($address)
            ->setSmsInforming($smsInformation)
            ->setEmailInforming($emailInformation)
            ->setDateBirth($dateBirthDate)
            ->setHeartAttackDate($heartAttackDate);
        $this->entityManager->persist($patient);

    }

    /**
     * Add Doctor Hospital
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     * @param string $positionName
     */
    public function addDoctorHospital(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled,
        string $positionName
    ): void
    {
        $this->addDoctor(
            $phone,
            $firstName,
            $lastName,
            $role,
            $password,
            $enabled,
            $positionName
        );
    }

    /**
     * add Doctor Consultant
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     * @param string $positionName
     */
    public function addDoctorConsultant(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled,
        string $positionName
    ): void
    {
        $this->addDoctor(
            $phone,
            $firstName,
            $lastName,
            $role,
            $password,
            $enabled,
            $positionName
        );
    }

    /**
     * Add Doctor
     */
    private function addDoctor(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled,
        string $positionName
    ): void
    {
        $position = $this->positionRepository->findOneBy(['name' => $positionName]);
        $this->addStaff(
            $phone,
            $firstName,
            $lastName,
            $role,
            $password,
            $enabled,
            $position
        );
    }

    /**
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     * @return AuthUser
     */
    private function addAuthUser(
        string $phone,
        string $firstName,
        string $lastName,
        string $role,
        string $password,
        bool   $enabled
    ): AuthUser
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
        $this->entityManager->persist($user);

        return $user;
    }

    /**
     * @param string $phone
     * @param string $firstName
     * @param string $lastName
     * @param string $role
     * @param string $password
     * @param bool $enabled
     * @param Position $position
     */
    private function addStaff(
        string   $phone,
        string   $firstName,
        string   $lastName,
        string   $role,
        string   $password,
        bool     $enabled,
        Position $position
    ): void
    {
        $authUser = $this->addAuthUser($phone, $firstName, $lastName, $role, $password, $enabled);
        $staff = (new Staff())
            ->setAuthUser($authUser)
            ->setPosition($position);
        $this->entityManager->persist($staff);
    }
}