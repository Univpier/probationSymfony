<?php

namespace App\Controller;

use App\Service\CreateTreeService;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
class CreateTreeController extends AbstractController
{
    private $service;

    public function __construct(CreateTreeService $service)
    {
        $this->service = $service;
    }
    /**
     * @Route("/tree", name="tree_show", methods="GET")
     */
    public function tree(): JsonResponse
    {
        return $this->json($this->service->getTree());
    }
    /**
     * @Route("/tree/pdf/{indent}", name="tree_pdf_show", methods="GET")
     */
    public function treePdf(int $indent): StreamedResponse
    {
        return new StreamedResponse($this->service->treePdf($indent), Response::HTTP_OK, [
            'Content-type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Table.pdf"',
            'Cache-Control'=>'max-age=0'
        ]);
    }
    /**
     * @Route("/table/pdf", name="tree_table_show", methods="GET")
     */
    public function treeTable(): StreamedResponse
    {
        return new StreamedResponse($this->service->treeTableDompdf(), Response::HTTP_OK, [
            'Content-type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Table.pdf"',
            'Cache-Control'=>'max-age=0'
        ]);
    }
}