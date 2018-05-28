<?php
namespace App\Middlewares;

use App\Auth\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TokenMiddleware {
	/**
	 * @var Session
	 */
	private $session;

	public function __construct(Session $session)
	{
		$this->session = $session;
	}

	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
	{
		if ($request->hasHeader('Authorization')){
			$auth = str_replace('Bearer ', '', $request->getHeader('Authorization'))[0];
			$session = $this->session->get($auth);
			if ($session !== NULL){
				return $next($request, $response);
			}else{
				return $response->withStatus(401)->withJson([
					'success' => false,
					'errors' => [
						[
							'message' => 'Authorization header is invalid',
							'code' => 'auth_header_invalid'
						]
					]
				]);
			}
		}else{
			return $response->withStatus(401)->withJson([
				'success' => false,
				'errors' => [
					[
						'message' => 'Authorization header is missing',
						'code' => 'auth_header_missing'
					]
				]
			]);
		}
	}
}