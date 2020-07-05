<?php


namespace Espo\Core\Utils\Api;

use \Espo\Core\Utils\Api\Slim;

class Auth extends \Slim\Middleware
{
    protected $auth;

    protected $authRequired = null;

    protected $showDialog = false;

    public function __construct(\Espo\Core\Utils\Auth $auth, $authRequired = null, $showDialog = false)
    {
        $this->auth = $auth;
        $this->authRequired = $authRequired;
        $this->showDialog = $showDialog;
    }

    function call()
    {
        $request = $this->app->request();

        $uri = $request->getResourceUri();
        $httpMethod = $request->getMethod();

        $username = $request->headers('PHP_AUTH_USER');
        $password = $request->headers('PHP_AUTH_PW');

        $authenticationMethod = null;

        $espoAuthorizationHeader = $request->headers('Http-App-Authorization');
        if (isset($espoAuthorizationHeader)) {
            list($username, $password) = explode(':', base64_decode($espoAuthorizationHeader), 2);
        } else {
            $hmacAuthorizationHeader = $request->headers('X-Hmac-Authorization');
            if ($hmacAuthorizationHeader) {
                $authenticationMethod = 'Hmac';
                list($username, $password) = explode(':', base64_decode($hmacAuthorizationHeader), 2);
            } else {
                $apiKeyHeader = $request->headers('X-Api-Key');
                if ($apiKeyHeader) {
                    $authenticationMethod = 'ApiKey';
                    $username = $apiKeyHeader;
                    $password = null;
                }
            }
        }

        if (!isset($username)) {
            if (!empty($_COOKIE['auth-username']) && !empty($_COOKIE['auth-token'])) {
                $username = $_COOKIE['auth-username'];
                $password = $_COOKIE['auth-token'];
            }
        }

        if (!isset($username) && !isset($password)) {
            $espoCgiAuth = $request->headers('Http-Espo-Cgi-Auth');
            if (empty($espoCgiAuth)) {
                $espoCgiAuth = $request->headers('Redirect-Http-Espo-Cgi-Auth');
            }
            if (!empty($espoCgiAuth)) {
                list($username, $password) = explode(':' , base64_decode(substr($espoCgiAuth, 6)));
            }
        }

        if (is_null($this->authRequired)) {
            $routes = $this->app->router()->getMatchedRoutes($httpMethod, $uri);

            if (!empty($routes[0])) {
                $routeConditions = $routes[0]->getConditions();
                if (isset($routeConditions['auth']) && $routeConditions['auth'] === false) {

                    if ($username && $password) {
                        try {
                            $isAuthenticated = $this->auth->login($username, $password);
                        } catch (\Exception $e) {
                            $this->processException($e);
                            return;
                        }
                        if ($isAuthenticated) {
                            $this->next->call();
                            return;
                        }
                    }

                    $this->auth->useNoAuth();
                    $this->next->call();
                    return;
                }
            }
        } else {
            if (!$this->authRequired) {
                $this->auth->useNoAuth();
                $this->next->call();
                return;
            }
        }

        if ($username) {
            try {
                $isAuthenticated = $this->auth->login($username, $password, $authenticationMethod);
            } catch (\Exception $e) {
                $this->processException($e);
                return;
            }

            if ($isAuthenticated) {
                $this->next->call();
            } else {
                $this->processUnauthorized();
            }
        } else {
            if (!$this->isXMLHttpRequest()) {
                $this->showDialog = true;
            }
            $this->processUnauthorized();
        }
    }

    protected function processException(\Exception $e)
    {
        $response = $this->app->response();

        if ($e->getMessage()) {
            $response->headers->set('X-Status-Reason', $e->getMessage());
        }
        $response->setStatus($e->getCode());
    }

    protected function processUnauthorized()
    {
        $response = $this->app->response();

        if ($this->showDialog) {
            $response->headers->set('WWW-Authenticate', 'Basic realm=""');
        }
        $response->setStatus(401);
    }

    protected function isXMLHttpRequest()
    {
        $request = $this->app->request();

        $httpXRequestedWith = $request->headers('Http-X-Requested-With');
        if ($httpXRequestedWith && strtolower($httpXRequestedWith) == 'xmlhttprequest') {
            return true;
        }

        return false;
    }
}
