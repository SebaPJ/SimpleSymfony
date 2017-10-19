<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="phones")
 */
class Email extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     *
     * @var string $address
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contact")
     * @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     *
     * @var Contact
     */
    private $contact;
}