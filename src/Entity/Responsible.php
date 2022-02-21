<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Responsible
 *
 * @ORM\Table(name="responsible")
 * @ORM\Entity(repositoryClass="App\Repository\ResponsibleRepository")
 */
class Responsible
{
    /**
     * @var int
     *
     * @ORM\Column(name="responsibleId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $responsibleid;

    /**
     * @var string
     *
     * @ORM\Column(name="responsible_name", type="string", length=50, nullable=false, options={"fixed"=true})
     */
    private $responsibleName;

    public function getResponsibleid(): ?int
    {
        return $this->responsibleid;
    }

    public function getResponsibleName(): ?string
    {
        return $this->responsibleName;
    }

    public function setResponsibleName(string $responsibleName): self
    {
        $this->responsibleName = $responsibleName;

        return $this;
    }


}
