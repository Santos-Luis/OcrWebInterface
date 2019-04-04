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
     * @throws \Google_Exception
     */
    public function index(Request $request): Response
    {
        $url = $request->get('url');

        $message = $this->imageValidation($url);
        if ($message === null) {
            $message = 'No fraud detected';
        }

        if ($message !== null) {
            $this->writeToSheet(1, 'www', 'abc');
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

        $texts = $response->getTextAnnotations();
        foreach ($texts as $text) {
            if (
                preg_match('/(\d((?!\d).)*){6}|@/', $text->getDescription())
                && !preg_match('/(\d+[\/\.](\d)(\d)[\/\.]\d+)/', $text->getDescription())
            ) {
                return $text->getDescription();
            }
        }

        return null;
    }

    /**
     * @param int    $propertyId
     * @param string $photoLink
     * @param string $textFounded
     *
     * @throws \Google_Exception
     */
    function writeToSheet(int $propertyId, string $photoLink, string $textFounded): void
    {
        $client = new \Google_Client();
        $client->setApplicationName('My PHP App');
        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig( __DIR__.'/../../keys/google-key.json');

        $sheets = new \Google_Service_Sheets($client);
        $spreadsheetId = '1_ZMhm2BbJFJNH-QGgGL-6EvzTJd6hAfH53WlWwxfrlw';

        $valueRange= new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues(['values' => [$propertyId, $photoLink, $textFounded]]);

        $range = 'A1';
        $conf = ['valueInputOption' => 'RAW'];

        $sheets->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $conf);
    }
}
