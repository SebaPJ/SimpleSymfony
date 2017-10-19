<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Phone;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ContactController extends AbstractController
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
     * @Route("/remove/{contactId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-remove")
     *
     * @param int $contactId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($contactId, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('default');

        /** @var Session $session */
        $session = $this->get('session');

        if (empty($contactId)) {
            $session->getFlashBag()->add('danger', 'Missing contact id for remove action');
            return $this->redirectToRoute('contacts-list');
        }

        $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);
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
     * @Route("/view/{contactId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-view")
     *
     * @param int $contactId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($contactId, Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        if (empty($contactId)) {
            $session->getFlashBag()->add('danger', 'Missing contact id for view action');
            return $this->redirectToRoute('contacts-list');
        }

        $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);
        if (empty($fetchedContact)) {
            $session->getFlashBag()->add('danger', 'Contact not found');
            return $this->redirectToRoute('contacts-list');
        }

        return $this->render('contact/view.html.twig', [
            'element' => $fetchedContact,
        ]);
    }

    /**
     * @Route("/edit/{contactId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-edit")
     *
     * @param int $contactId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($contactId = null, Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        $form = $this->prepareContactForm($contactId);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            if (null === $contactId) {
                $session->getFlashBag()->add('success', 'Contact properly created');
            } else {
                $session->getFlashBag()->add('success', 'Contact properly modified');
            }

            return $this->redirectToRoute('contacts-list');
        }

        return $this->render('contact/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $contactId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function prepareContactForm($contactId = null)
    {
        if (null === $contactId) {
            $contact = new Contact();
        } else {
            $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);

            $contact = null === $fetchedContact ? (new Contact()) : $fetchedContact;
        }

        $form = $this->createFormBuilder($contact)
            ->add('name', TextType::class)
            ->add('surname', TextType::class)
            ->add('save', SubmitType::class, ['label' => null === $contact->getId() ? 'Create Contact' : 'Modify Contact'])
            ->getForm();

        return $form;
    }
}
