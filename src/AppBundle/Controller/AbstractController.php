<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Memory;
use AppBundle\Entity\Phone;
use AppBundle\Entity\RememberEntity;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractController extends Controller
{
    /**
     * Method saves information about action.
     *
     * @param string $name
     * @param Contact $contact
     * @param string $description
     */
    public function remember(string $name, Contact $contact, string $description)
    {
        $memory = new Memory();
        $memory->setContact($contact);
        $memory->setName($name);
        $memory->setDescription($description);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('default');

        $em->persist($memory);
        $em->flush($memory);
    }
}
