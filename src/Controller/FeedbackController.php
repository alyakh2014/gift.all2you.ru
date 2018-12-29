<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * @Route("/feedback")
 */
class FeedbackController extends AbstractController
{
    /**
     * @Route("/", name="feedback_index", methods="GET")
     */
    public function index(FeedbackRepository $feedbackRepository): Response
    {
        return $this->render('feedback/index.html.twig', ['feedback' => $feedbackRepository->findAll()]);
    }

    /**
     * @Route("/new", name="feedback_new", methods="GET|POST")
     */
    public function new(
        Request $request,
        \Swift_Mailer $mailer
    ): Response
    {

        //Формируем форму такую как на странице contacts
        $feedback = new Feedback();
        $form = $this->createFormBuilder($feedback)
            ->setAction($this->generateUrl('feedback_new'))
            ->add('name', TextType::class, array('label'=>'Your name'))
            ->add('email', TextType::class)
            ->add('subject', TextareaType::class)
            ->add('message', TextareaType::class)
            ->add('save', SubmitType::class, array('label'=>'Send new message'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($feedback);
            $em->flush();
            $this->addFlash(
                'notice',
                'Ваше обращение принято!'
            );

            // создание объекта сообщения
            //mail('alyakh2014@gmail.com', 'Just mail', 'Just message body');

            $message = (new \Swift_Message("Hello mail"))
                ->setFrom('info@gift.all2you.ru')
                ->setTo($form->get("email")->getData())
                ->setSubject($form->get("subject")->getData())
                ->setBody($this->renderView(
                    'emails/feedback.html.twig',
                    array('name' => $form->get("name")->getData(), 'message'=>$form->get("message")->getData())
                ),
                    'text/html'
                );
            // отправка сообщения
            $mailer->send($message);

            return $this->redirectToRoute('about_contacts');
        }

        return $this->render('feedback/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feedback_show", methods="GET")
     */
    public function show(Feedback $feedback): Response
    {
        return $this->render('feedback/show.html.twig', ['feedback' => $feedback]);
    }

    /**
     * @Route("/{id}/edit", name="feedback_edit", methods="GET|POST")
     */
    public function edit(Request $request, Feedback $feedback): Response
    {
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('feedback_edit', ['id' => $feedback->getId()]);
        }

        return $this->render('feedback/edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="feedback_delete", methods="DELETE")
     */
    public function delete(Request $request, Feedback $feedback): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($feedback);
            $em->flush();
        }

        return $this->redirectToRoute('feedback_index');
    }
}
