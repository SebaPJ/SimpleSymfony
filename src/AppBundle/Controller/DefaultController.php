<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-list")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em    = $this->get('doctrine')->getManager('default');
        $dql   = "SELECT a FROM AppBundle:Contact a ORDER BY a.id";
        $query = $em->createQuery($dql);

        $paginator  = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            $request->query->getInt('limit', 20)/*limit per page*/
        );

        return $this->render('contact/list.html.twig', ['pagination' => $pagination]);
    }

    /**
     * @Route("/add{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-add")
     */
    public function addAction(Request $request)
    {
        $this->getDoctrine()->getRepository(Contact::class)->findAll();
        return $this->render('contact/list.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/remove/{contact}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-remove")
     *
     * @param Contact $contact
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction(Contact $contact, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('default');

        /** @var Session $session */
        $session = $this->get('session');

        if (empty($contact->getId())) {
            $session->getFlashBag()->add('danger', 'Missing contact id for remove action');
            return $this->redirectToRoute('contacts-list');
        }

        $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contact->getId());
        if (empty($fetchedContact)) {
            $session->getFlashBag()->add('danger', 'Contact not found');
            return $this->redirectToRoute('contacts-list');
        }

        $em->remove($fetchedContact);
        $em->flush();

        $session->getFlashBag()->add('success', 'Contact successfully removed');
        return $this->redirectToRoute('contacts-list');
    }

    /**
     * @Route("/edit/{contact}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-edit")
     */
    public function editAction(Contact $contact, Request $request)
    {
//        $this->getDoctrine()->getRepository(Contact::class)->findAll();
//        return $this->render('contact/list.html.twig', [
//            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
//        ]);
    }


}
