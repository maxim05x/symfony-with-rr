<?php

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     * @Route("/default/hello/{name}")
     *
     * @param string $name
     * @return Response
     */
    public function hello(string $name)
    {
        return new Response($name);
    }
}
