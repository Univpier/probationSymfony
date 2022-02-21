<?php

namespace App\Controller;

use App\Dto\TestDto;
use App\Service\CreateTreeService;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/tree", name="tree", methods="GET")
     */
    public function tree(): JsonResponse
    {
        return $this->json($this->service->getTree());
    }
    /**
     * @Route("/newItem", name="newItem", methods="POST")
     */
    public function newItems(Request $request): JsonResponse
    {
        $dto = new TestDto();
        $dto->parentid = $request->get('parentid');
        $dto->name = $request->get('name');
        return $this->json($this->service->newItem($dto));
    }
    /**
     * @Route("/pdf/tree", name="treePdf", methods="GET")
     */
    public function treePdf(): StreamedResponse
    {
        return new StreamedResponse($this->service->treePdf(), Response::HTTP_OK, [
            'Content-type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Table.pdf"',
            'Cache-Control'=>'max-age=0'
        ]);
    }
    /**
     * @Route("/pdf/table", name="tablePdf", methods="GET")
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