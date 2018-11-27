<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07-11-18
 * Time: 18:41
 */

namespace App\Controller;


use App\Entity\Gallery;
use App\Entity\Service;
use App\Entity\Team;
use App\Form\GalleryType;
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
class GalleryController extends AbstractController
{
    /**
     * @Route("/gallery", name="admin-gallery", methods="GET")
     */
    public function showServices(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Gallery::class);
            $data = $em->findAll();

            return $this->render("admin/gallery/admin.gallery.html.twig", ["galleries" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/gallery/{id}", name="admin-gallery-detail", methods="GET")
     * @param int $id
     * @return Response
     */
    public function showService(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Gallery::class);
            $data = $em->find($id);

            $form = $this->createForm(GalleryType::class, $data);

            return $this->render("admin/gallery/admin.gallery.details.html.twig", ["form" => $form->createView(), "gallery" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/gallery", name="admin-gallery-modify", methods="POST")
     * @return Response
     */
    public function modifyService(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $gallery = new Gallery();
            $form = $this->createForm(GalleryType::class, $gallery);
            $form->handleRequest($request);



            if($form->isSubmitted()){

                $gallery->setId($request->request->get("gallery")["id"]);

                $em = $this->getDoctrine()->getRepository(Gallery::class);
                $data = $em->find($gallery->getId());

                if($data != null){

                    if ($request->files->count() > 0) {
                        if($request->files->get("gallery")["imgpath"] != null) {
                            foreach ($request->files as $uploadedFile) {
                                $file = $uploadedFile["imgpath"];
                                $filename = uniqid() . "." . $file->getClientOriginalExtension();
                                if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {
                                    $webPath = $this->getParameter('kernel.project_dir') . '/public/large-gallery/';
                                    $file->move($webPath, $filename);

                                    $data->setImgpath("dummy/large-gallery/" . $filename);
                                }
                            }
                        }
                    }

                    $data->setDesc($gallery->getDesc());
                    $data->setTitle($gallery->getTitle());
                    $data->setCategory($gallery->getCategory());

                    $manager = $this->getDoctrine()->getManager();

                    $manager->persist($data);
                    $manager->flush();

                    $this->addFlash("success", "La photo de galerie a été modifié avec succès !");

                }
                else{
                    $this->addFlash("danger", "La photo de galerie n'a pas été trouvé !");
                }
            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-gallery");
    }

    /**
     * @Route("/gallery-add", name="admin-gallery-add", methods="GET")
     * @return Response
     */
    public function showAddService(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {
            $data = new Gallery();
            $form = $this->createForm(GalleryType::class, $data);

            return $this->render("admin/gallery/admin.gallery.add.html.twig", ["form" => $form->createView()]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/gallery-add", name="admin-gallery-new", methods="POST")
     * @return Response
     */
    public function addService(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $gallery = new Gallery();
            $form = $this->createForm(GalleryType::class, $gallery);
            $form->handleRequest($request);

            if($form->isSubmitted()) {

                $data = new Gallery();

                if ($request->files->count() > 0) {
                    if ($request->files->get("gallery")["imgpath"] != null) {
                        foreach ($request->files as $uploadedFile) {
                            $file = $uploadedFile["imgpath"];
                            $filename = uniqid() . "." . $file->getClientOriginalExtension();
                            if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {
                                $webPath = $this->getParameter('kernel.project_dir') . '/public/dummy/large-gallery/';
                                $file->move($webPath, $filename);

                                $data->setImgpath("dummy/large-gallery/" . $filename);
                            }
                        }
                    }
                }

                $data->setDesc($gallery->getDesc());
                $data->setTitle($gallery->getTitle());
                $data->setCategory($gallery->getCategory());

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($data);
                $manager->flush();

                $this->addFlash("success", "Le service a été ajouté avec succès !");

            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-gallery");
    }

    /**
     * @Route("/gallery/{id}", name="admin-gallery-delete", methods="DELETE")
     * @param int $id
     * @return Response
     */
    public function deleteService(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Gallery::class);
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

        return $this->redirectToRoute("admin-gallery");
    }
}