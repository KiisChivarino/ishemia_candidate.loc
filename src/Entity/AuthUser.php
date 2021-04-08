<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Пользователь
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(options={"comment":"Пользователь"});
 */
class AuthUser implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"comment"="Ключ пользователя"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, nullable=true, options={"comment"="Email пользователя"})
     */
    private $email;

    /**
     * @ORM\Column(type="json", options={"comment"="Роли пользователя"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true, options={"comment"="Пароль пользователя"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10, unique=true, options={"comment"="Телефон пользователя"}, columnDefinition="CHAR(10) CHECK (LENGTH(phone) = 10)")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=30, options={"comment"="Имя пользователя"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100, options={"comment"="Фамилия пользователя"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true, options={"comment"="Отчество пользователя"})
     */
    private $patronymicName;

    /**
     * @ORM\Column(type="boolean", options={"comment"="Ограничение использования", "default"=true})
     */
    private $enabled;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="authUserSender")
     */
    private $notifications;

    /**
     * AuthUser constructor.
     */
    public function __construct()
    {
        $this->notifications = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRoles(string $role): self
    {
        $this->roles = [$role];
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return $this
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = mb_convert_case($firstName, MB_CASE_TITLE, "UTF-8");
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = mb_convert_case($lastName, MB_CASE_TITLE, "UTF-8");
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPatronymicName(): ?string
    {
        return $this->patronymicName;
    }

    /**
     * @param string|null $patronymicName
     *
     * @return $this
     */
    public function setPatronymicName(?string $patronymicName): self
    {
        $this->patronymicName = mb_convert_case($patronymicName, MB_CASE_TITLE, "UTF-8");
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setAuthUserSender($this);
        }

        return $this;
    }

    /**
     * @param Notification $notification
     * @return $this
     */
    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getAuthUserSender() === $this) {
                $notification->setAuthUserSender(null);
            }
        }

        return $this;
    }
}
