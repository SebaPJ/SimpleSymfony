<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Entity\Phone;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class PhoneController extends AbstractController
{
    /**
     * @Route("/contact/{contactId}/call/{phoneId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-phones-call")
     * @param $contactId
     * @param $phoneId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function callAction($contactId, $phoneId, Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        if (empty($contactId) || empty($phoneId)) {
            $session->getFlashBag()->add('danger', 'Missing data for message');
            return $this->redirectToRoute('contacts-list');
        }

        /** @var Contact $fetchedContact */
        $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);
        if (empty($fetchedContact)) {
            $session->getFlashBag()->add('danger', 'Contact not found');
            return $this->redirectToRoute('contacts-list');
        }

        /** @var Phone $fetchedPhone */
        $fetchedPhone = $this->getDoctrine()->getRepository(Phone::class)->find($phoneId);
        if (empty($fetchedPhone)) {
            $session->getFlashBag()->add('danger', 'Phone not found');
            return $this->redirectToRoute('contacts-list');
        }

        $this->remember('call', $fetchedContact, 'Call made to: ' . $fetchedPhone->getNumber());

        /** @var Session $session */
        $session = $this->get('session');
        $session->getFlashBag()->add('success', 'Call made successfully');

        return $this->redirectToRoute('contacts-list');
    }

    /**
     * @Route("/contact/{contactId}/message/{phoneId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-phones-message")
     *
     * @param $contactId
     * @param $phoneId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function messageAction($contactId, $phoneId, Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        if (empty($contactId) || empty($phoneId)) {
            $session->getFlashBag()->add('danger', 'Missing data for message');
            return $this->redirectToRoute('contacts-list');
        }

        /** @var Contact $fetchedContact */
        $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);
        if (empty($fetchedContact)) {
            $session->getFlashBag()->add('danger', 'Contact not found');
            return $this->redirectToRoute('contacts-list');
        }

        /** @var Phone $fetchedPhone */
        $fetchedPhone = $this->getDoctrine()->getRepository(Phone::class)->find($phoneId);
        if (empty($fetchedPhone)) {
            $session->getFlashBag()->add('danger', 'Phone not found');
            return $this->redirectToRoute('contacts-list');
        }

        $this->remember('message', $fetchedContact, 'Messages: example message, sent to ' . $fetchedPhone->getNumber());

        /** @var Session $session */
        $session = $this->get('session');
        $session->getFlashBag()->add('success', 'Call made successfully');

        return $this->redirectToRoute('contacts-list');
    }

    /**
     * @Route("/contacts/{contactId}/remove/{phoneId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-phones-remove")
     *
     * @param int $contactId
     * @param int $phoneId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeAction($contactId, $phoneId, Request $request)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager('default');

        /** @var Session $session */
        $session = $this->get('session');

        if (empty($phoneId)) {
            $session->getFlashBag()->add('danger', 'Missing phone id for remove action');
            return $this->redirectToRoute('contacts-view', ['contactId' => $contactId]);
        }

        $fetchedPhone = $this->getDoctrine()->getRepository(Phone::class)->find($phoneId);
        if (empty($fetchedPhone)) {
            $session->getFlashBag()->add('danger', 'Phone not found');
            return $this->redirectToRoute('contacts-view', ['contactId' => $contactId]);
        }

        $em->remove($fetchedPhone);
        $em->flush();

        $session->getFlashBag()->add('success', 'Phone successfully removed');
        return $this->redirectToRoute('contacts-view', ['contactId' => $contactId]);
    }

    /**
     * @Route("/contacts/{contactId}/edit/{phoneId}{trailingSlash}", requirements={"trailingSlash" = "[/]{0,1}"}, defaults={"trailingSlash" = "/"}, name="contacts-phones-edit")
     *
     * @param int $contactId
     * @param int $phoneId
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($contactId = null, $phoneId = null, Request $request)
    {
        /** @var Session $session */
        $session = $this->get('session');

        $form = $this->preparePhoneForm($phoneId);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Phone $phone */
            $phone = $form->getData();

            $em = $this->getDoctrine()->getManager();

            /** @var Contact $fetchedContact */
            $fetchedContact = $this->getDoctrine()->getRepository(Contact::class)->find($contactId);
            if (empty($fetchedContact)) {
                $session->getFlashBag()->add('danger', 'Contact not found');
                return $this->redirectToRoute('contacts-list');
            }

            $phone->setContact($fetchedContact);
            $em->persist($phone);
            $em->flush();

            if (null === $phoneId) {
                $session->getFlashBag()->add('success', 'Phone properly added');
            } else {
                $session->getFlashBag()->add('success', 'Phone properly modified');
            }

            return $this->redirectToRoute('contacts-view', ['contactId' => $contactId]);
        }

        return $this->render('phone/edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $phoneId
     * @return \Symfony\Component\Form\FormInterface
     */
    public function preparePhoneForm($phoneId = null)
    {
        if (null === $phoneId) {
            $phone = new Phone();
        } else {
            $fetchedPhone = $this->getDoctrine()->getRepository(Phone::class)->find($phoneId);

            $phone = null === $fetchedPhone ? (new Phone()) : $fetchedPhone;
        }

        $form = $this->createFormBuilder($phone)
            ->add('number', TextType::class)
            ->add('save', SubmitType::class, ['label' => null === $phone->getId() ? 'Create Phone' : 'Modify Phone'])
            ->getForm();

        return $form;
    }
}
