<?php

namespace App\Http\Controller;

use App\Http\Service\Finalizer;
use App\Http\Service\Model\AbstractResource;
use App\Http\Transformer\TransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    /**
     * @var Finalizer
     */
    private $finalizer;

    public function __construct(Finalizer $finalizer)
    {
        $this->finalizer = $finalizer;
    }

    public function resource($data, TransformerInterface $transformer = null): AbstractResource
    {
        return $this->finalizer->resource($data, $transformer);
    }

    public function flushChanges()
    {
        $this->getDoctrine()->getManager()->flush();
    }
}
