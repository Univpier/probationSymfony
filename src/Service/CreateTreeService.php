<?php

namespace App\Service;

use App\Dto\TestDto;
use App\Entity\Test;
use App\Repository\TestRepository;
use Doctrine\DBAL\Exception;
use Dompdf\Dompdf;
use Fpdf\Fpdf;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;

class CreateTreeService
{
    public $repository;
    private $entityManager;
    private $twig;
    public function __construct(TestRepository $repository, EntityManagerInterface $entityManager, Environment $twig){
        $this->repository = $repository;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    /**
     * @param int $parent_id
     * @param $objects
     * @return array|null
     */
    public function buildTree($objects, int $parent_id = 0)
    {
        if (isset($objects[$parent_id])){
            $dataObjects = [];
            foreach ($objects[$parent_id] as $object){
                $dataObject = [];
                $dataObject['id'] = $object->getId();
                $dataObject['parent_id'] = $object->getParentId();
                $dataObject['name'] = $object->getName();
                $dataObject['children'] = $this->buildTree($objects, $object->getId());
                $dataObjects[] = $dataObject;
            }
            return $dataObjects;
        } else {
            return null;
        }
    }

    public function getTree(): array
    {
        $items = $this->repository->findAll();
        if (empty($items)) {
            throw new Exception(json_encode(['approved'=> false, 'err' => 'missing items']), 404);
        }
        $objects = [];
        foreach ($items as $item) {
            $objects[$item->getParentId()][] = $item;
        }
        return $this->buildTree($objects);
    }

    public function newItem(TestDto $dto)
    {
        try{
            $this->entityManager->getConnection()->beginTransaction();
            $item = new Test();
            var_dump($dto); ;
            $item->setParentId($dto->parentid);
            $item->setName($dto->name);

            $this->entityManager->persist($item);
            $this->entityManager->flush();
            $this->entityManager->commit();

            return (['approved' => true, 'message' => 'New item added '.$item->getId()]);
        } catch (Exception $e){
            $this->entityManager->rollback();
            throw $e;
        }
    }
    /**
     * @param int $parent_id
     * @param $objects
     * @param $level
     * @param $tree_text
     * @return string
     */
    public function seeTreePdf($parentId, $level, $tree, $objects) //Обработка массива
    {

        if (isset($objects[$parentId])){
            foreach ($objects[$parentId] as $object){
                $tree .= "<div style='margin-left:" . ($level * 30) . "px;'>" . $object->getName() . "</div>";
                $level++;
                $tree .= $this->seeTreePdf($object->getId(), $level, '', $objects);
                $level--;
            }
            return $tree;
        } else {
            return '';
        }
    }
    public function treePdf()
    {
        $items = $this->repository->findAll();
        if (empty($items)) {
            throw new Exception(json_encode(['approved'=> false, 'err' => 'missing items']), 404);
        }
        $objects = array();
        foreach ($items as $item) {
            $objects[$item->getParentId()][] = $item;
        }
        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->seeTreePdf(0, 0, '', $objects));
        $dompdf->setPaper('A4');
        $dompdf->render();
        return function () use ($dompdf) { $dompdf->stream('treePdf.pdf'); };
    }


    public function treeTableDompdf(){
        $items = $this->repository->findAll();
        if (empty($items)) {
            throw new Exception(json_encode(['approved'=> false, 'err' => 'missing items']), 404);
        }
        $html = $this->twig->render('pdf.html.twig', ['items' => $items]);
        $dompdf = new DOMPDF();
        $dompdf->loadhtml($html);
        $dompdf->render();
        return function () use ($dompdf) { $dompdf->stream('Table.pdf'); };
    }
}