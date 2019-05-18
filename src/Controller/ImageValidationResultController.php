<?php

namespace App\Controller;

use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageValidationResultController extends AbstractController
{
    private const QUEUE_URL = 'https://sqs.eu-west-1.amazonaws.com/750336674511/dev_workshop';
    private const DELAY_SECONDS = '10';

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
            $this->sendMessageToQueue();
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
        $response = $imageAnnotator->documentTextDetection($image);

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

    private function sendMessageToQueue(): void
    {
        $client = new SqsClient([
            'profile' => 'default',
            'region' => 'eu-west-1',
            'version' => '2012-11-05'
        ]);

        $message = ['property_id' => '123'];
        $arguments = [
            'QueueUrl'      => self::QUEUE_URL,
            'MessageBody'   => json_encode($message),
            'DelaySeconds'  => self::DELAY_SECONDS
        ];

        try {
            $result = $client->sendMessage($arguments);
        } catch (AwsException $e) {
            error_log($e->getMessage());
        }
    }
}
