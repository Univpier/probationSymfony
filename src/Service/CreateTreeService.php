<?php

namespace App\Service;

use App\Repository\TestRepository;
use App\Entity\Test;
use Doctrine\DBAL\Exception;
use Dompdf\Dompdf;
use Fpdf\Fpdf;
use Twig\Environment;

class CreateTreeService
{
    public $repository;
    private $twig;
    public function __construct(TestRepository $repository, Environment $twig){
        $this->repository = $repository;
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
                $dataObject = array();
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
        $elements = $this->repository->findAll();

        if (empty($elements)) {
            throw new Exception(json_encode(['success'=> false, 'msg' => 'missing items']), 404);
        }
        $objects = [];
        foreach ($elements as $element) {
            $objects[$element->getParentId()][] = $element;
        }
        return $this->buildTree($objects);
    }

    public function seeTreePdf($parent_id, $level, $tree_text, $objects, $indent)
    {
        //todo: вынести в запрос отступ
        if (isset($objects[$parent_id])){
            foreach ($objects[$parent_id] as $object){
                $tree_text .= str_repeat("-", $level * $indent) . $object->getName() . "<br>";
                $level++;
                $tree_text .= $this->seeTreePdf($object->getId(), $level, '', $objects, $indent);
                $level--;
            }
            return $tree_text;
        } else {
            return '';
        }
    }
    public function treePdf(int $indent)
    {
        $elements = $this->repository->findAll();

        if (empty($elements)) {
            throw new Exception(json_encode(['success'=> false, 'msg' => 'missing items']), 404);
        }

        $objects = array();
        foreach ($elements as $element) { //Обходим массив
            $objects[$element->getParentId()][] = $element;
        }

        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->seeTreePdf(0, 0, '', $objects, $indent));
        $dompdf->setPaper('A4');
        $dompdf->render();

        return function () use ($dompdf) { $dompdf->stream('Tree.pdf'); };
    }
    public function treeTable()
    {
        $elem = $this->repository->findAll();

        if (empty($elements)) {
            throw new Exception(json_encode(['success'=> false, 'msg' => 'missing items']), 404);
        }

        $pdf = new Fpdf();
        $pdf->AddPage();
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,7,'id', 1);
        $pdf->Cell(30,7,'name', 1);
        $pdf->Cell(30,7,'parentId', 1);
        $pdf->Ln();
        foreach ($elem as $row){
            $pdf->Cell(30,7,$row->getId(), 1);
            $pdf->Cell(30,7,$row->getName(), 1);
            $pdf->Cell(30,7,$row->getParentId(), 1);
            $pdf->Ln();
        }
        return $pdf->Output('D', 'doc.pdf');
    }
    public function treeTableDompdf(){
        $elements = $this->repository->findAll();

        if (empty($elements)) {
            throw new Exception(json_encode(['success'=> false, 'msg' => 'missing items']), 404);
        }
        $html = $this->twig->render('pdf.html.twig', ['elements' => $elements]);
        $dompdf = new DOMPDF();

        $dompdf->loadhtml($html);
        $dompdf->render();
        return function () use ($dompdf) { $dompdf->stream('Table.pdf'); };
    }
}