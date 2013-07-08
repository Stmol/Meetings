<?php

namespace Stmol\HuddleBundle\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\Role;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Member
 *
 * @ORM\Table(name="members")
 * @ORM\Entity(repositoryClass="Stmol\HuddleBundle\Entity\Repository\MemberRepository")
 */
class Member implements UserInterface, EquatableInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=40, nullable=true)
     */
    private $secret;

    /**
     * @ORM\OneToMany(targetEntity="MemberMeetingRole", mappedBy="member")
     */
    private $meetingRelations;

    public function __construct()
    {
        $this->createDate = new \DateTime();
        $this->secret = sha1(uniqid(mt_rand(), true));
        $this->meetingRelations = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Member
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Member
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     * @return Member
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Member
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Add meetingRelation
     *
     * @param MemberMeetingRole $meetingRelation
     * @return Member
     */
    public function addMeetingRelation(MemberMeetingRole $meetingRelation)
    {
        $this->meetingRelations[] = $meetingRelation;
    
        return $this;
    }

    /**
     * Remove meetingRelation
     *
     * @param MemberMeetingRole $meetingRelation
     */
    public function removeMeetingRelation(MemberMeetingRole $meetingRelation)
    {
        $this->meetingRelations->removeElement($meetingRelation);
    }

    /**
     * Get meetingRelations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMeetingRelations()
    {
        return $this->meetingRelations;
    }

    /**
     * Set secret
     *
     * @param string $secret
     * @return Member
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    
        return $this;
    }

    /**
     * Get secret
     *
     * @return string 
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return Boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Member) {
            return false;
        }

        if ($this->secret !== $user->getUsername()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
            return false;
        }

        if ($this->name !== $user->getName()) {
            return false;
        }

        if ($this->surname !== $user->getSurname()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        return array();
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->secret;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     *
     * @return void
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}