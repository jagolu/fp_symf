<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Player
 *
 * @ORM\Table(name="player", indexes={@ORM\Index(name="id_team_fk", columns={"id_team"})})
 * @ORM\Entity
 */
class Player
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_player", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPlayer;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @var int
     *
     * @ORM\Column(name="goals", type="integer", nullable=false)
     */
    private $goals;

    /**
     * @var int
     *
     * @ORM\Column(name="shots", type="integer", nullable=false)
     */
    private $shots;

    /**
     * @var int
     *
     * @ORM\Column(name="passes", type="integer", nullable=false)
     */
    private $passes;

    /**
     * @var int
     *
     * @ORM\Column(name="assits", type="integer", nullable=false)
     */
    private $assits;

    /**
     * @var int
     *
     * @ORM\Column(name="recoveries", type="integer", nullable=false)
     */
    private $recoveries;

    /**
     * @var int
     *
     * @ORM\Column(name="goals_conceded", type="integer", nullable=false)
     */
    private $goalsConceded;

    /**
     * @var \Team
     *
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_team", referencedColumnName="id_team")
     * })
     */
    private $idTeam;

    public function getIdPlayer(): ?int
    {
        return $this->idPlayer;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getGoals(): ?int
    {
        return $this->goals;
    }

    public function setGoals(int $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    public function getShots(): ?int
    {
        return $this->shots;
    }

    public function setShots(int $shots): self
    {
        $this->shots = $shots;

        return $this;
    }

    public function getPasses(): ?int
    {
        return $this->passes;
    }

    public function setPasses(int $passes): self
    {
        $this->passes = $passes;

        return $this;
    }

    public function getAssits(): ?int
    {
        return $this->assits;
    }

    public function setAssits(int $assits): self
    {
        $this->assits = $assits;

        return $this;
    }

    public function getRecoveries(): ?int
    {
        return $this->recoveries;
    }

    public function setRecoveries(int $recoveries): self
    {
        $this->recoveries = $recoveries;

        return $this;
    }

    public function getGoalsConceded(): ?int
    {
        return $this->goalsConceded;
    }

    public function setGoalsConceded(int $goalsConceded): self
    {
        $this->goalsConceded = $goalsConceded;

        return $this;
    }

    public function getIdTeam(): ?Team
    {
        return $this->idTeam;
    }

    public function setIdTeam(?Team $idTeam): self
    {
        $this->idTeam = $idTeam;

        return $this;
    }


}
