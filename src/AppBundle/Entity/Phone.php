<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="phones")
 */
class Phone extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     * @var string $number
     */
    private $number;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *
     * @var Contact
     */
    private $contact;

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @param string $number
     */
    public function setNumber(string $number)
    {
        $this->number = $number;
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact
    {
        return $this->contact;
    }

    /**
     * @param Contact $contact
     */
    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
    }
}