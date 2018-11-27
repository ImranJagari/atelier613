<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07-11-18
 * Time: 18:41
 */

namespace App\Controller;


use App\Entity\Team;
use App\Form\TeamType;
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
class TeamController extends AbstractController
{
    /**
     * @Route("/team", name="admin-team", methods="GET")
     */
    public function showTeam(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Team::class);
            $data = $em->findAll();

            return $this->render("admin/team/admin.team.html.twig", ["team" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/team/{id}", name="admin-member", methods="GET")
     * @param int $id
     * @return Response
     */
    public function showTeamMember(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Team::class);
            $data = $em->find($id);

            $form = $this->createForm(TeamType::class, $data);

            return $this->render("admin/team/admin.team.details.html.twig", ["form" => $form->createView(), "member" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/team", name="admin-member-modify", methods="POST")
     * @return Response
     */
    public function modifyTeamMember(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $member = new Team();
            $form = $this->createForm(TeamType::class, $member);
            $form->handleRequest($request);



            if($form->isSubmitted()){

                $member->setId($request->request->get("team")["id"]);

                $em = $this->getDoctrine()->getRepository(Team::class);
                $data = $em->find($member->getId());

                if($data != null){

                    if ($request->files->count() > 0) {
                        if($request->files->get("team")["imgpath"] != null) {
                            foreach ($request->files as $uploadedFile) {
                                $file = $uploadedFile["imgpath"];
                                $filename = uniqid() . "." . $file->getClientOriginalExtension();
                                if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {
                                    $webPath = $this->getParameter('kernel.project_dir') . '/public/dummy/';
                                    $file->move($webPath, $filename);

                                    $data->setImgpath("dummy/" . $filename);
                                }
                            }
                        }
                    }

                    $data->setFirstname($member->getFirstname());
                    $data->setLastname($member->getLastname());

                    $manager = $this->getDoctrine()->getManager();

                    $manager->persist($data);
                    $manager->flush();

                    $this->addFlash("success", "Le membre a été modifié avec succès !");

                }
                else{
                    $this->addFlash("danger", "Le membre n'a pas été trouvé !");
                }
            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-team");
    }
    /**
     * @Route("/team-add", name="admin-member-add", methods="GET")
     * @return Response
     */
    public function showAddTeamMember(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {
            $data = new Team();
            $form = $this->createForm(TeamType::class, $data);

            return $this->render("admin/team/admin.team.add.html.twig", ["form" => $form->createView()]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/team-add", name="admin-member-new", methods="POST")
     * @return Response
     */
    public function addTeamMember(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $member = new Team();
            $form = $this->createForm(TeamType::class, $member);
            $form->handleRequest($request);

            if($form->isSubmitted()) {

                $data = new Team();

                if ($request->files->count() > 0) {
                    if ($request->files->get("team")["imgpath"] != null) {
                        foreach ($request->files as $uploadedFile) {
                            $file = $uploadedFile["imgpath"];
                            $filename = uniqid() . "." . $file->getClientOriginalExtension();
                            if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {
                                $webPath = $this->getParameter('kernel.project_dir') . '/public/dummy/';
                                $file->move($webPath, $filename);

                                $data->setImgpath("dummy/" . $filename);
                            }
                        }
                    }
                }

                $data->setFirstname($member->getFirstname());
                $data->setLastname($member->getLastname());

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($data);
                $manager->flush();

                $this->addFlash("success", "Le membre a été ajouté avec succès !");

            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-team");
    }
    /**
     * @Route("/team/{id}", name="admin-member-delete", methods="DELETE")
     * @param int $id
     * @return Response
     */
    public function deleteTeamMember(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Team::class);
            $data = $em->find($id);
            if($data != null) {
                $webPath = $this->getParameter('kernel.project_dir') . '/public/' . $data->getImagepath();

                $filesystem = new Filesystem();
                $filesystem->remove($webPath);

                $manager = $this->getDoctrine()->getManager();
                $manager->remove($data);
                $manager->flush();
            }
        }

        return $this->redirectToRoute("admin-team");
    }
}