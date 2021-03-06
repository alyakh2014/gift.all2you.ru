<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\RoleRepository;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAdmin;

    /**
     * @ORM\Column(type="smallint")
     */
    private $userage;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $gender;

    // *  ** @ORM\Column(type="string", length=20, nullable=true)
    //private $role;

    /**
     * Many User have Many Roles
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(
     *      name="user_roles",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     * )
     */
    private $roles;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string")
     */
    private $controlString;

    public function __construct()
    {
        $this->isActive = true;
        $this->setControlString($this->generateCStr());
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getUserage(): ?int
    {
        return $this->userage;
    }

    public function setUserage(int $userage): self
    {
        $this->userage = $userage;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function addRole(Role $role)
    {
        $this->roles->add($role);
    }

    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);
    }

    public function getRoles()
    {
        if(!empty($this->roles->toArray())){
            foreach($this->roles->toArray() as $key=>&$tmpR){
                $tempRole[] = $tmpR->getName();
            }
            return $tempRole;
        }else{
            return array('ROLE_USER');
        }

    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->userage,
            $this->isActive,
            $this->isAdmin,
            $this->gender,
            $this->password,
            // см. раздел о соли ниже
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->userage,
            $this->isActive,
            $this->isAdmin,
            $this->gender,
            $this->password,
            // см. раздел о соли ниже
            // $this->salt
            ) = unserialize($serialized);
    }

    public function getSalt()
    {
        // вам *может* понадобиться настоящая соль в зависимости от вашего кодировщика
        // см. раздел о соли ниже
        return null;
    }

    public function setControlString($str): self
    {
        $this->controlString = $str;
        return $this;
    }

    public function getControlString(): ?string
    {
        return $this->controlString;
    }

    public function generateCStr(){
        return md5(random_int(0,100).time().random_int(100,999));
    }

    public function eraseCredentials()
    {
    }
}
