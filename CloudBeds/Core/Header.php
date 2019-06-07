<?php
/**
 * Created by PhpStorm.
 * User: jack
 * Date: 6/4/19
 * Time: 9:53 PM
 */

namespace Core;
use Symfony\Component\HttpFoundation\{Request, Response};

class Header
{
    private $request;
    private $response;

    private $charset = 'UTF-8';
    /**
     * Header constructor.
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function sendHeader($content = '')
    {
        if (is_array($content))
            $this->setJsonContent($content);
        else
            $this->setHtmlContent($content);

        $this->response->setCharset($this->charset);
        $this->response->send();

        return $this;
    }

    public function sendNotFound($content = '')
    {
        $this->response->setStatusCode(Response::HTTP_NOT_FOUND);
        $this->sendHeader($content);

        return $this;
    }


    public function sendMethodNotAllowed($content = '')
    {
        $this->response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->sendHeader($content);

        return $this;
    }

    public function sendCode($content = '', int $code)
    {
        $this->response->setStatusCode($code);
        $this->sendHeader($content);

        return $this;
    }


    public function setHeader(int $statusCode)
    {
        $this->response->setStatusCode($statusCode);

        return $this;
    }


    public function setCharset(string $charset)
    {
        $this->charset = $charset;
        $this->response->setCharset($this->charset);

        return $this;
    }

    public function setJsonContent(array $data)
    {
        $this->response->setContent(json_encode($data));
        $this->response->headers->set('Content-Type', 'application/json');

        return $this;
    }


    public function setHtmlContent(string $data)
    {
        $this->response->setContent($data);
        $this->response->headers->set('Content-Type', 'text/html');

        return $this;
    }

}