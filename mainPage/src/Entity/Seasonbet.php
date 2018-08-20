<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Seasonbet
 *
 * @ORM\Table(name="seasonbet", indexes={@ORM\Index(name="goals_fk", columns={"goals"}), @ORM\Index(name="shots_fk", columns={"shots"}), @ORM\Index(name="passes_fk", columns={"passes"}), @ORM\Index(name="recoveries_fk", columns={"recoveries"}), @ORM\Index(name="goals_conceded_fk", columns={"goals_conceded"}), @ORM\Index(name="assits_fk", columns={"assits"}), @ORM\Index(name="room_fk", columns={"id_room"}), @ORM\Index(name="IDX_D371287A6B3CA4B", columns={"id_user"})})
 * @ORM\Entity
 */
class Seasonbet
{
    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;

    /**
     * @var \Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="assits", referencedColumnName="id_player")
     * })
     */
    private $assits;

    /**
     * @var \Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="goals_conceded", referencedColumnName="id_player")
     * })
     */
    private $goalsConceded;

    /**
     * @var \Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="goals", referencedColumnName="id_player")
     * })
     */
    private $goals;

    /**
     * @var \Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="passes", referencedColumnName="id_player")
     * })
     */
    private $passes;

    /**
     * @var \Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recoveries", referencedColumnName="id_player")
     * })
     */
    private $recoveries;

    /**
     * @var \Room
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_room", referencedColumnName="id_room")
     * })
     */
    private $idRoom;

    /**
     * @var \Player
     *
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="shots", referencedColumnName="id_player")
     * })
     */
    private $shots;

    /**
     * @var \User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     * })
     */
    private $idUser;

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getAssits(): ?Player
    {
        return $this->assits;
    }

    public function setAssits(?Player $assits): self
    {
        $this->assits = $assits;

        return $this;
    }

    public function getGoalsConceded(): ?Player
    {
        return $this->goalsConceded;
    }

    public function setGoalsConceded(?Player $goalsConceded): self
    {
        $this->goalsConceded = $goalsConceded;

        return $this;
    }

    public function getGoals(): ?Player
    {
        return $this->goals;
    }

    public function setGoals(?Player $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    public function getPasses(): ?Player
    {
        return $this->passes;
    }

    public function setPasses(?Player $passes): self
    {
        $this->passes = $passes;

        return $this;
    }

    public function getRecoveries(): ?Player
    {
        return $this->recoveries;
    }

    public function setRecoveries(?Player $recoveries): self
    {
        $this->recoveries = $recoveries;

        return $this;
    }

    public function getIdRoom(): ?Room
    {
        return $this->idRoom;
    }

    public function setIdRoom(?Room $idRoom): self
    {
        $this->idRoom = $idRoom;

        return $this;
    }

    public function getShots(): ?Player
    {
        return $this->shots;
    }

    public function setShots(?Player $shots): self
    {
        $this->shots = $shots;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }


}
