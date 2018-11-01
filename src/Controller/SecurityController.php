<?php
/**
 * Created by PhpStorm.
 * User: Hudenko
 * Date: 25.10.2018
 * Time: 13:43
 */

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Entity\User;

class SecurityController extends AbstractController
{

    private $security;
    /**
     * @param Request $request
     * @Route("/login", name="login")
     */
    public function loginAction(
        Request $request,
        AuthenticationUtils $authUtils): Response
    {

        //Если пользователь уже авторизован
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('user_edit');
        }

        //Получаем параметр редиректа
        $redirectTo = $request->query->get('redirect_to');
        if(!$redirectTo || $redirectTo == ''){
            $redirectTo = 'user_edit';
        }
        // получить ошибку входа, если она есть
        $error = $authUtils->getLastAuthenticationError();

        // последнее имя пользователя, введенное пользователем
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
            'redirect_params' =>$redirectTo
        ));
    }

    /**
     * @Route("/account", name="account")
     */
    public function redirectToAccount(Request $request): Response
    {
        $user = new User();
        return new Response(
            "<h1>".$user->getEmail()."</h1>"
        );
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
     * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}