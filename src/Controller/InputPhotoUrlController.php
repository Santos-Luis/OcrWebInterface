<?php

namespace App\Controller;

use App\Entity\PhotoUrl;
use App\Form\InputPhotoUrlType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * InputPhotoUrlController
 */
class InputPhotoUrlController extends AbstractController
{
    /**
     * @Route("/inputPhoto", name="input_photo_url")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $url = new PhotoUrl();

        $form = $this->createForm(
            InputPhotoUrlType::class,
            $url,
            [ 'action' => $this->generateUrl('input_photo_url') ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            return $this->redirectToRoute('image_validation_result', [ 'url' => $url->getUrl() ]);
        }

        return $this->render('input_photo_url/index.html.twig', [
            'imageUrl' => $form->createView()
        ]);
    }
}
