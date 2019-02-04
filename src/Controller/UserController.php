<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/profile")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(UserRepository $userRepository, AuthorizationCheckerInterface $authChecker): Response
    {
        //Если пользователь уже авторизован
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('blog_index');
        }elseif(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('user_edit');
        }
        return $this->render('user/index.html.twig', ['users' => $userRepository->findAll()]);
    }

    /**
     * @Route("/new", name="user_new", methods="GET|POST")
     */
    public function userNew(Request $request,  AuthorizationCheckerInterface $authChecker): Response
    {

        if(!$authChecker->isGranted('ROLE_ADMIN')){
            return $this->redirectToRoute('login');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newPass", name="user_recoverpath", methods="GET|POST")
     */
    public function userRecoverPassword(Request $request, \Swift_Mailer $mailer): Response
    {
        //Создаём форму
        $form = $this->createFormBuilder()
            ->add('email', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Пробуем найти пользвателя с таким email
            $submittedEmail = $form->getData()['email'];
            $exitstUsers = $this->getDoctrine()->getRepository(User::class);
            $exitstUser = $exitstUsers->findOneBy(['email' => $submittedEmail]);
            if($exitstUser && $exitstUser->getId() > 0 ){
            //Отсылаем письмо
              $url2recover_pass = 'gift.all2you.ru/profile/change-password/?cstr='.$exitstUser->getControlString().'&pr=Y&um='.$submittedEmail;
                $message = (new \Swift_Message("Восстановление пароля на сайте gift.all2you.ru"))
                    ->setFrom('info@gift.all2you.ru')
                    ->setTo($submittedEmail)
                    ->setSubject("Восстановление пароля на сайте gift.all2you.ru")
                    ->setBody($this->renderView(
                        'emails/recovery_pass.html.twig',
                        array('name' => $exitstUser->getUsername(), 'control_string'=>$url2recover_pass)
                    ),
                        'text/html'
                    );
                // отправка сообщения
                $mailer->send($message);
                // создание объекта сообщения

                $this->addFlash(
                    'notice',
                    'На Ваш email отправлены рекоммендации для смены пароля!'
                );

            }else{
                $this->addFlash(
                    'notice',
                    'Пользователь с таким email не существует!'
                );

                return $this->render(
                    'user/recovery_password.html.twig',
                    array('form' => $form->createView())
                );
            }
        }else{
            $this->addFlash(
                'notice',
                'Возможно Вы не правильно ввели пароль. Повторите попытку!'
            );

            return $this->render(
                'user/recovery_password.html.twig',
                array('form' => $form->createView())
            );
        }

        return $this->render(
            'user/recovery_password.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("/edit", name="user_edit", methods="GET|POST")
     */
    public function edit(Request $request, AuthorizationCheckerInterface $authChecker): Response
    {

      if($request->get('id') && $authChecker->isGranted('ROLE_ADMIN')){
          $id = $request->get('id');
         $user = $this->getDoctrine()
              ->getRepository(User::class)
              ->findOneBy(["id" => $id]);
      }else{
          $user = $this->getUser();
      }


        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'notice',
                'Профиль успешно отредактирован!'
            );
            return $this->redirectToRoute('user_edit', ['id' => $id]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password", name="user_change_password",  methods={"GET", "POST"})
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {

        $user = $this->getUser();
        if(!$user && $request->query->get('pr') == 'Y' && $request->query->get('um')){
           $requestEmail = $request->query->get('um');
           $exitstUsers = $this->getDoctrine()->getRepository(User::class);
           $user = $exitstUsers->findOneBy(['email' => $requestEmail, 'controlString' => $request->query->get('cstr')]);
        }

        $form = $this->createFormBuilder()
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->getForm();
         $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid() && $user) {
             $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
             $user->setControlString($user->generateCStr());
             $this->getDoctrine()->getManager()->flush();
             $this->addFlash(
                 'notice',
                 'Пароль успешно измен!'
             );

             return $this->redirectToRoute('user_change_password');
         }

         return $this->render('user/change_password.html.twig', [
             'form' => $form->createView(),
         ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        return $this->redirectToRoute('user_index');//$this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

}
