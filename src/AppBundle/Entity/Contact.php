<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="contacts")
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Email", mappedBy="contact", cascade={"remove", "merge"})
     *
     * @var ArrayCollection
     */
    private $emails;

    /**
     * One BusinessObject has Many Contact Persons.
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Phone", mappedBy="contact", cascade={"remove", "merge"})
     *
     * @var ArrayCollection
     */
    private $phones;

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
     * @return Phone[]|ArrayCollection
     */
    public function getPhones() : ArrayCollection
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

    /**
     * @param Email[]|ArrayCollection $emails
     */
    public function setEmails($emails)
    {
        $this->emails = new ArrayCollection();
        foreach ($emails as $email) {
            $this->emails->add($email);
        }
    }

    /**
     * @return Email[]|ArrayCollection
     */
    public function getEmails() : ArrayCollection
    {
        return $this->emails ?: $this->emails = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addEmail(Email $email) : Contact
    {
        if (!$this->getEmails()->contains($email)) {
            $this->getEmails()->add($email);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeEmail(Email $email) : Contact
    {
        if ($this->getEmails()->contains($email)) {
            $this->getEmails()->removeElement($email);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailsAddresses() : array
    {
        $names = [];
        foreach ($this->getEmails() as $email) {
            $names[] = $email->getContent();
        }

        return $names;
    }
}