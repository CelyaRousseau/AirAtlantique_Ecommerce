<?php
//src/AirAtlantique/UserBundle/Entity/User.php
Namespace AirAtlantique\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="AirAtlantique\UserBundle\Entity\UserRepository")
 */

class User extends BaseUser
{
        /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    protected $id;
    
	/**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=10)
     * @var string
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     * @var string
     */
    private $country;

    /**
    * @ORM\OneToOne(targetEntity="AirAtlantique\UserBundle\Entity\MembershipCard", mappedBy="user")
    */
    private $membershipCard;

    /**
    * @ORM\Column(type="string", length=16, nullable=true)
    * @var string
    */
    private $creditCard;

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
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set gender
     *
     * @param string $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set membershipCard
     *
     * @param \AirAtlantique\UserBundle\Entity\MembershipCard $membershipCard
     * @return User
     */
    public function setMembershipCard(\AirAtlantique\UserBundle\Entity\MembershipCard $membershipCard = null)
    {
        $this->membershipCard = $membershipCard;

        return $this;
    }

    /**
     * Get membershipCard
     *
     * @return \AirAtlantique\UserBundle\Entity\MembershipCard 
     */
    public function getMembershipCard()
    {
        return $this->membershipCard;
    }

    /**
     * Set creditCard
     *
     * @param \AirAtlantique\UserBundle\Entity\CreditCard $creditCard
     * @return User
     */
    public function setCreditCard($creditCard = null)
    {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * Get creditCard
     *
     * @return \AirAtlantique\UserBundle\Entity\CreditCard 
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->email,
            $this->lastName,
            $this->firstName,
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.

        list(
            $this->email,
            $this->lastName,
            $this->firstName,
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
        ) = $data;
    }
}
