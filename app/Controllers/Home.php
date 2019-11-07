<?php //declare(strict_types = 1);

namespace Controllers;

use Http\HttpRequest;
use Http\HttpResponse;


class Home
{
    private $response;
    private $request;

    public function __construct(HttpRequest $request,HttpResponse $response)
    {
        $this->response = $response;
        $this->request = $request;
    }

    public function list()
    {
        $content = '<h1>Hello World</h1>';
        $content .= 'Hello ' . $this->request->getParameter('name', 'stranger');
        return $content;
    }
}