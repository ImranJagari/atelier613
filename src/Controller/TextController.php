<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 07-11-18
 * Time: 18:42
 */

namespace App\Controller;


use App\Entity\Text;
use App\Form\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class TextController extends AbstractController
{
    /**
     * @Route("/text", name="admin-text", methods="POST")
     */
    public function modifyContent(Request $request, SessionInterface $session): Response
    {

        if ($session->isStarted() && $session->get("user") != null) {
            if ($request->files->count() > 0) {

                foreach ($request->files as $uploadedFile) {
                    $filename = $request->request->get("text")["filename"];
                    $file = $uploadedFile["saloon"];
                    if (preg_match('/jpeg|png|jpg|gif/', $file->getClientOriginalExtension())) {

                        $text = $this->getDoctrine()
                            ->getRepository(Text::class)
                            ->findOneBy(['key' => $request->request->get("text")["key"]]);
                        if ($text != null) {
                            $webPath = $this->getParameter('kernel.project_dir') . '/public/dummy/';
                            $file->move($webPath, $filename);
                            $text->setFrench($webPath . $filename);

                            $manager = $this->getDoctrine()->getManager();
                            $manager->persist($text);
                            $manager->flush();
                        }
                    }
                }
            } else {

                $text = new Text();
                $form = $this->createForm(TextType::class, $text);
                $form->handleRequest($request);

                $em = $this->getDoctrine()->getRepository(Text::class);
                $data = $em->findOneBy(['key' => $text->getKey()]);

                if ($data == null) {
                    $this->addFlash("danger", "Le texte que vous essayez de modifier n'existe pas, veuillez contacter l'administrateur du site !");
                } else {

                    $data->setFrench($text->getFrench() == null ? "" : $text->getFrench());

                    $manager = $this->getDoctrine()->getManager();
                    $manager->persist($data);
                    $manager->flush();

                    $this->addFlash("success", "Le texte a été modifié avec succès !");
                }
            }
        }

        return $this->redirectToRoute("admin-index");
    }
}