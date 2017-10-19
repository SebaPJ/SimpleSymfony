<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="contacts")
 * @ORM\Entity()
 */
class Contact extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     * @var string $name
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string $surname
     */
    private $surname;

    /**
     * One BusinessObject has Many Contact Persons.
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Phone", mappedBy="contact", cascade={"remove", "merge"})
     *
     * @var ArrayCollection
     */
    private $phones;

    public function __construct()
    {
        $this->name = '';
        $this->surname = '';
        $this->phones = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname(): string
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname)
    {
        $this->surname = $surname;
    }

    /**
     * @param Phone[]|ArrayCollection $phones
     */
    public function setPhones($phones)
    {
        $this->phones = new ArrayCollection();
        foreach ($phones as $phone) {
            $this->phones->add($phone);
        }
    }

    /**
     * @return Phone[]|ArrayCollection|Collection
     */
    public function getPhones() : Collection
    {
        return $this->phones ?: $this->phones = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addPhone(Phone $phone) : Contact
    {
        if (!$this->getPhones()->contains($phone)) {
            $this->getPhones()->add($phone);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removePhone(Phone $phone) : Contact
    {
        if ($this->getPhones()->contains($phone)) {
            $this->getPhones()->removeElement($phone);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhonesNumbers() : array
    {
        $names = [];
        foreach ($this->getPhones() as $phone) {
            $names[] = $phone->getNumber();
        }

        return $names;
    }
}