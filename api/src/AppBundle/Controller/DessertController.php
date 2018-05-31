<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use AppBundle\Entity\Dessert4;
use AppBundle\Form\Dessert4Type;


class DessertController extends Controller {

    /**
     * @Rest\View()
     * @Rest\Get("/dessert")
     */
    public function getDessertsAction(Request $request) {
        $Dessert = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Dessert4')
                ->findAll();

        return $Dessert;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/dessert")
     */
    public function postDessertAction(Request $request) {
        $Dessert = new Dessert4();
        $form = $this->createForm(Dessert4Type::class, $Dessert);

        $form->submit($request->request->all());

        if ($form->isValid()) {      	
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($Dessert);
            $em->flush();
            return $Dessert;
        } else {
            return $form;
        }
    }

     /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/dessert/{id}")
     */
    public function removeDessertAction(Request $request) {
        $em = $this->get('doctrine.orm.entity_manager');
        $Dessert = $em->getRepository('AppBundle:Dessert4')
                    ->find($request->get('id'));

        if ($Dessert) {
            $em->remove($Dessert);
            $em->flush();
        }
    }


    /**
     * @Rest\View()
     * @Rest\Put("/dessert/{id}")
     */
    public function updateDessertAction(Request $request) {
        $Dessert = $this->get('doctrine.orm.entity_manager')
                ->getRepository('AppBundle:Dessert4')
                ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire

        if (empty($Dessert)) {
            return new JsonResponse(
                ['message' => 'Dessert non trouvé'], 
                Response::HTTP_NOT_FOUND
            );
        }
       $form = $this->createForm(Dessert4Type::class, $Dessert);

         // Le paramètre false dit à Symfony de garder les valeurs dans notre 
         // entité si l'utilisateur n'en fournit pas une dans sa requête
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            // l'entité vient de la base, donc le merge n'est pas nécessaire.
            // il est utilisé juste par soucis de clarté
            $em->merge($Dessert);
            $em->flush();
            return $Dessert;
        } else {
            return $form;
        }
    }      

}