<?php
/**
 * Created by PhpStorm.
 * User: Hudenko
 * Date: 25.10.2018
 * Time: 14:40
 */

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Role;


class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {

        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_edit');
        }

        // 1) постройте форму
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) обработайте отправку (произойдёт только в POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $exitstUsers = $this->getDoctrine()->getRepository(User::class);
            $exitstUser = $exitstUsers->findOneBy(['email' => $form->getData()->getEmail()]);
            if($exitstUser && $exitstUser->getId() > 0 ){
                $this->addFlash(
                    'notice',
                    'Пользователь с таким email уже существует!'
                );

                return $this->render(
                    'registration/register.html.twig',
                    array('form' => $form->createView())
                );
            }

            // 3) Зашифруйте пароль (вы также можете сделать это через слушатель Doctrine)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            //Устанавливаем пользователю роль по умолчанию. 2 - роль пользователя
            $defrole = $this->getDoctrine()
                ->getRepository(Role::class)
                ->findAll(['name' => 'ROLE_USER']);

            foreach($defrole as $role):
             $user->setRoles([$role]);
            endforeach;
            // 4) сохраните Пользователя!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash(
                'notice',
                'Вы успешно зарегистрировались! Пожалуйста, авторизуйтесь!'
            );
            //Отправка письма об успешной регистрации


            // может, установите "флеш" сообщение об успешном выполнении для пользователя
            $message = (new \Swift_Message("Регистрация на сайте gift.all2you.ru"))
                ->setFrom('info@gift.all2you.ru')
                ->setTo($form->getData()->getEmail())
                ->setSubject("Спасибо за регистрация на сайте gift.all2you.ru")
                ->setBody($this->renderView(
                    'emails/registration.html.twig',
                    array('name' => $form->getData()->getUsername())
                ),
                    'text/html'
                );
            // отправка сообщения
            $mailer->send($message);
            // создание объекта сообщения

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }
}