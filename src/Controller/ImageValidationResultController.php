<?php

namespace App\Controller;

use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageValidationResultController extends AbstractController
{
    /**
     * @Route("/result", name="image_validation_result")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     */
    public function index(Request $request): Response
    {
        $url = $request->get('url');

        $message = $this->imageValidation($url);
        if ($message === null) {
            $message = 'No fraud detected';
        }

        return $this->render(
            'image_validation_result/index.html.twig',
            [ 'message' => $message, 'path' => $url ]
        );

    }

    /**
     * @param string $url
     *
     * @return string|null
     *
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     */
    private function imageValidation(string $url): ?string
    {
        $imageAnnotator = new ImageAnnotatorClient();
        $temp = tmpfile();

        file_put_contents(stream_get_meta_data($temp)['uri'], fopen($url, 'rb'));
        $path = stream_get_meta_data($temp)['uri'];

        $image = file_get_contents($path);
        $response = $imageAnnotator->textDetection($image);

        $imageAnnotator->close();
        fclose($temp);
        file_put_contents('php://stderr', 'hello, this is a test4!\n');

        $texts = $response->getTextAnnotations();
        foreach ($texts as $text) {
            if (preg_match('/[1-9]|@/', $text->getDescription())) {
                return $text->getDescription();
            }
        }

        return null;
    }
}
