<?php

namespace App\Controller;

use App\Entity\Partners;
use App\Form\PartnersType;
use App\Repository\PartnersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
/**
 * @Route("/partners")
 */
class PartnersController extends AbstractController
{
    /**
     * @Route("/", name="partners_index", methods="GET")
     */
    public function index(PartnersRepository $partnersRepository,  AuthorizationCheckerInterface $authChecker): Response
    {
        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('about_partners');
        }

        return $this->render('partners/index.html.twig', ['partners' => $partnersRepository->findAll()]);
    }

    /**
     * @Route("/new", name="partners_new", methods="GET|POST")
     */
    public function new(Request $request,  AuthorizationCheckerInterface $authChecker): Response
    {

        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('about_partners');
        }
        $partner = new Partners();
        $form = $this->createForm(PartnersType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($partner);
            $em->flush();

            return $this->redirectToRoute('partners_index');
        }

        return $this->render('partners/new.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partners_show", methods="GET")
     */
    public function show(Partners $partner,  AuthorizationCheckerInterface $authChecker): Response
    {
        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('about_partners');
        }
        return $this->render('partners/show.html.twig', ['partner' => $partner]);
    }

    /**
     * @Route("/{id}/edit", name="partners_edit", methods="GET|POST")
     */
    public function edit(Request $request, Partners $partner,  AuthorizationCheckerInterface $authChecker): Response
    {
        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('about_partners');
        }
        $form = $this->createForm(PartnersType::class, $partner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('partners_edit', ['id' => $partner->getId()]);
        }

        return $this->render('partners/edit.html.twig', [
            'partner' => $partner,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="partners_delete", methods="DELETE")
     */
    public function delete(Request $request, Partners $partner): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partner->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($partner);
            $em->flush();
        }

        return $this->redirectToRoute('partners_index');
    }
}
