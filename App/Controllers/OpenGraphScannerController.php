<?php

namespace App\Controllers;

use App\MetasParser\Document;
use GuzzleHttp\Client;
use Masterminds\HTML5;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Validator\Validator;

class OpenGraphScannerController extends Controller
{
	public function getScan(ServerRequestInterface $request, ResponseInterface $response, Client $client)
	{
		$validator = new Validator($request->getQueryParams());
		$validator->required('url');
		$validator->notEmpty('url');
		$validator->url('url');
		if ($validator->isValid()) {
			try {
				$queryResponse = $client->get($validator->getValue('url'));
			} catch (\Exception $exception) {

				return $response->withJson([
					'success' => false,
					'errors' => [
						'Error while requesting the url'
					]
				]);
			}
			$html = $queryResponse->getBody()->getContents();
			$dom = new HTML5();
			$dom = $dom->loadHTML($html);
			$document = new Document($dom);
			$document->parse();

			return $response->withJson([
				'success' => true,
				'data' => [
					'parsed' => $document->toArray(),
					'meta' => $document->getMetas()
				]
			]);
		} else {
			return $response->withJson([
				'success' => false,
				'errors' => $validator->getErrors()
			])->withStatus(400);
		}
	}
}
