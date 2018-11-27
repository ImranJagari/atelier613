<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07-11-18
 * Time: 18:41
 */

namespace App\Controller;


use App\Entity\Service;
use App\Entity\Team;
use App\Form\ServiceType;
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
class ServiceController extends AbstractController
{
    /**
     * @Route("/service", name="admin-service", methods="GET")
     */
    public function showServices(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Service::class);
            $data = $em->findAll();

            return $this->render("admin/service/admin.service.html.twig", ["services" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/service/{id}", name="admin-service-detail", methods="GET")
     * @param int $id
     * @return Response
     */
    public function showService(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Service::class);
            $data = $em->find($id);

            $form = $this->createForm(ServiceType::class, $data);

            return $this->render("admin/service/admin.service.details.html.twig", ["form" => $form->createView(), "service" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/service", name="admin-service-modify", methods="POST")
     * @return Response
     */
    public function modifyService(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $service = new Service();
            $form = $this->createForm(ServiceType::class, $service);
            $form->handleRequest($request);



            if($form->isSubmitted()){

                $service->setId($request->request->get("service")["id"]);

                $em = $this->getDoctrine()->getRepository(Service::class);
                $data = $em->find($service->getId());

                if($data != null){

                    if ($request->files->count() > 0) {
                        if($request->files->get("service")["imgpath"] != null) {
                            foreach ($request->files as $uploadedFile) {
                                $file = $uploadedFile["imgpath"];
                                $filename = uniqid() . "." . $file->getClientOriginalExtension();
                                if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {
                                    $webPath = $this->getParameter('kernel.project_dir') . '/public/images/';
                                    $file->move($webPath, $filename);

                                    $data->setImgpath("images/" . $filename);
                                }
                            }
                        }
                    }

                    $data->setDesc($service->getDesc());
                    $data->setTitle($service->getTitle());

                    $manager = $this->getDoctrine()->getManager();

                    $manager->persist($data);
                    $manager->flush();

                    $this->addFlash("success", "Le service a été modifié avec succès !");

                }
                else{
                    $this->addFlash("danger", "Le service n'a pas été trouvé !");
                }
            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-service");
    }

    /**
     * @Route("/service-add", name="admin-service-add", methods="GET")
     * @return Response
     */
    public function showAddService(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {
            $data = new Service();
            $form = $this->createForm(ServiceType::class, $data);

            return $this->render("admin/service/admin.service.add.html.twig", ["form" => $form->createView()]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/service-add", name="admin-service-new", methods="POST")
     * @return Response
     */
    public function addService(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $service = new Service();
            $form = $this->createForm(ServiceType::class, $service);
            $form->handleRequest($request);

            if($form->isSubmitted()) {

                $data = new Service();

                if ($request->files->count() > 0) {
                    if ($request->files->get("service")["imgpath"] != null) {
                        foreach ($request->files as $uploadedFile) {
                            $file = $uploadedFile["imgpath"];
                            $filename = uniqid() . "." . $file->getClientOriginalExtension();
                            if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {
                                $webPath = $this->getParameter('kernel.project_dir') . '/public/images/';
                                $file->move($webPath, $filename);

                                $data->setImgpath("images/" . $filename);
                            }
                        }
                    }
                }

                $data->setDesc($service->getDesc());
                $data->setTitle($service->getTitle());

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($data);
                $manager->flush();

                $this->addFlash("success", "Le service a été ajouté avec succès !");

            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-service");
    }

    /**
     * @Route("/service/{id}", name="admin-service-delete", methods="DELETE")
     * @param int $id
     * @return Response
     */
    public function deleteService(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Service::class);
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

        return $this->redirectToRoute("admin-service");
    }
}