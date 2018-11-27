<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 04-11-18
 * Time: 17:11
 */

namespace App\Controller;


use App\Entity\Team;
use App\Entity\Text;
use App\Entity\User;
use App\Form\TeamType;
use App\Form\TextType;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin-index", methods="GET")
     */
    public function index(SessionInterface $session): Response
    {
        if (!$session->isStarted() || $session->get("user") == null) {

            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            return $this->render('admin/admin.login.html.twig', ["form" => $form->createView()]);
        } else {

            $text = new Text();
            $form = $this->createForm(TextType::class, $text);

            $em = $this->getDoctrine()
                ->getRepository(Text::class)
                ->findAll();

            return $this->render('admin/admin.index.html.twig', ["form" => $form->createView(), "data" => $em]);
        }
    }

    /**
     * @Route("/login", name="admin-login", methods="POST")
     */
    public function login(Request $request, SessionInterface $session): Response
    {
        if (!$session->isStarted() || $session->get("user") == null) {

            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $em = $this->getDoctrine()->getRepository(User::class);
                $data = $em->findOneBy(['username' => $user->getUsername()]);

                if ($data == null) {
                    $this->addFlash("danger", "Le compte n'existe pas !");
                } else if ($data->getPassword() !== hash('sha512',$user->getPassword())) {
                    $this->addFlash("danger", "Le mot de passe n'est pas valide !");
                } else {
                    $session->set('user', $user);
                }
            }
        }
        return $this->redirectToRoute("admin-index");
    }
}