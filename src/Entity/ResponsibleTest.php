<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResponsibleTest
 *
 * @ORM\Table(name="responsible_test", indexes={@ORM\Index(name="test_id", columns={"test_id"}), @ORM\Index(name="responsible_id", columns={"responsible_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ResponsibleTestRepository")
 */
class ResponsibleTest
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Responsible
     *
     * @ORM\ManyToOne(targetEntity="Responsible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsible_id", referencedColumnName="responsibleId")
     * })
     */
    private $responsible;

    /**
     * @var \Test
     *
     * @ORM\ManyToOne(targetEntity="Test")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="test_id", referencedColumnName="id")
     * })
     */
    private $test;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponsible(): ?Responsible
    {
        return $this->responsible;
    }

    public function setResponsible(?Responsible $responsible): self
    {
        $this->responsible = $responsible;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): self
    {
        $this->test = $test;

        return $this;
    }


}
