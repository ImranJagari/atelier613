<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07-11-18
 * Time: 20:56
 */

namespace App\Controller;


use App\Entity\Price;
use App\Entity\Service;
use App\Entity\Team;
use App\Form\PriceType;
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
class PriceController extends AbstractController
{
    /**
     * @Route("/price", name="admin-price", methods="GET")
     */
    public function showPrices(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Price::class);
            $data = $em->findAll();

            return $this->render("admin/price/admin.price.html.twig", ["prices" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/price/{id}", name="admin-price-detail", methods="GET")
     * @param int $id
     * @return Response
     */
    public function showPrice(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Price::class);
            $data = $em->find($id);

            $form = $this->createForm(PriceType::class, $data);

            return $this->render("admin/price/admin.price.details.html.twig", ["form" => $form->createView(), "price" => $data]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/price", name="admin-price-modify", methods="POST")
     * @return Response
     */
    public function modifyPrice(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $price = new Price();
            $form = $this->createForm(PriceType::class, $price);
            $form->handleRequest($request);



            if($form->isSubmitted()){

                $price->setId($request->request->get("price")["id"]);

                $em = $this->getDoctrine()->getRepository(Price::class);
                $data = $em->find($price->getId());

                if($data != null){

                    $data->setPrice($price->getPrice());
                    $data->setService($price->getService());
                    $data->setSex($price->getSex());

                    $manager = $this->getDoctrine()->getManager();

                    $manager->persist($data);
                    $manager->flush();

                    $this->addFlash("success", "Le prix a été modifié avec succès !");

                }
                else{
                    $this->addFlash("danger", "Le prix n'a pas été trouvé !");
                }
            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-price");
    }

    /**
     * @Route("/price-add", name="admin-price-add", methods="GET")
     * @return Response
     */
    public function showAddPrice(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {
            $data = new Price();
            $form = $this->createForm(PriceType::class, $data);

            return $this->render("admin/price/admin.price.add.html.twig", ["form" => $form->createView()]);
        }

        return $this->redirectToRoute("admin-index");
    }
    /**
     * @Route("/price-add", name="admin-price-new", methods="POST")
     * @return Response
     */
    public function addPrice(Request $request, SessionInterface $session): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $price = new Price();
            $form = $this->createForm(PriceType::class, $price);
            $form->handleRequest($request);

            if($form->isSubmitted()) {

                $data = new Price();

                $data->setPrice($price->getPrice());
                $data->setService($price->getService());
                $data->setSex(intval($request->request->get("price")["sex"]));

                $manager = $this->getDoctrine()->getManager();

                $manager->persist($data);
                $manager->flush();

                $this->addFlash("success", "Le prix a été ajouté avec succès !");

            }
            else{
                $this->addFlash("danger", "Le formulaire  n'a pas été envoyé");
            }
        }

        return $this->redirectToRoute("admin-price");
    }

    /**
     * @Route("/price/{id}", name="admin-price-delete", methods="DELETE")
     * @param int $id
     * @return Response
     */
    public function deletePrice(Request $request, SessionInterface $session, int $id): Response
    {
        if ($session->isStarted() && $session->get("user") != null) {

            $em = $this->getDoctrine()->getRepository(Price::class);
            $data = $em->find($id);
            if($data != null) {

                $manager = $this->getDoctrine()->getManager();
                $manager->remove($data);
                $manager->flush();
            }
        }

        return $this->redirectToRoute("admin-price");
    }
}