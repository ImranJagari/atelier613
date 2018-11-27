<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 31-10-18
 * Time: 11:13
 */

namespace App\Controller;


use App\Entity\Gallery;
use App\Entity\Price;
use App\Entity\Service;
use App\Entity\Team;
use App\Entity\Text;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods="GET")
     */
    public function index() : Response{
        $emText = $this->getDoctrine()
            ->getRepository(Text::class)
            ->find(1);

        $emTeam = $this->getDoctrine()
            ->getRepository(Team::class)
            ->findAll();

        $emService = $this->getDoctrine()
            ->getRepository(Service::class)
            ->findAll();

        return $this->render('index.html.twig', ["info" => $emText->getFrench(), "team" => $emTeam, "services" => $emService]);

    }
    /**
     * @Route("/prices", name="prices", methods="GET")
     */
    public function prices() : Response {
        $em = $this->getDoctrine()
            ->getRepository(Text::class)
            ->find(2);
        $emPrice = $this->getDoctrine()
            ->getRepository(Price::class)
            ->findAll();

        return $this->render('prices.html.twig', ["price" => $em->getFrench(), "prices" => $emPrice]);
    }
    /**
     * @Route("/gallery", name="gallery", methods="GET")
     */
    public function gallery() : Response {

        $emGallery = $this->getDoctrine()
            ->getRepository(Gallery::class)
            ->findAll();
        $categories = $this->getCategories($emGallery);
        return $this->render('gallery.html.twig', ["galleries" => $emGallery, "categories" => $categories]);
    }
    /**
     * @Route("/contact", name="contact", methods="GET")
     */
    public function contact() : Response {
        $em = $this->getDoctrine()
            ->getRepository(Text::class)
            ->find(3);
        return $this->render('contact.html.twig', ["contact" => $em->getFrench()]);
    }

    private function getCategories($emGallery) : array {
        $array = [];
        foreach ($emGallery as $gallery){
            if(!in_array($gallery->getCategory(), $array)){
                array_push($array, $gallery->getCategory());
            }
        }
        return $array;
    }
}