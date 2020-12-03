<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity()
 * @ApiResource(
 *     security="is_granted('ROLE_USER')",
 *     collectionOperations={
 *      "get" = {"normalization_context"={"groups"={"match:Collection:Read"}}},
 *      "post"
 *     },
 *     itemOperations={
 *          "get",
 *          "put" = {"security"="is_granted('ROLE_ADMIN')"},
 *          "delete" = {"security"="is_granted('ROLE_ADMIN')"},
 *     }
 * )
 */
class Match
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_PLAYING = 'playing';
    public const STATUS_OVER = 'over';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;
    /**
     * @ORM\ManyToOne(targetEntity=Player::class)
     */
    private ?Player $playerA = null;
    /**
     * @ORM\ManyToOne(targetEntity=Player::class)
     */
    private ?Player $playerB = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $scorePlayerA = null;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $scorePlayerB = null;
    /**
     * @ORM\Column(type="string")
     * @Groups("match:Collection:Read")
     */
    private ?string $status = null;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return Player
     */
    public function getPlayerA(): ?Player
    {
        return $this->playerA;
    }

    /**
     * @param Player $playerA
     */
    public function setPlayerA(Player $playerA): void
    {
        $this->playerA = $playerA;
    }

    /**
     * @return Player
     */
    public function getPlayerB(): ?Player
    {
        return $this->playerB;
    }

    /**
     * @param Player $playerB
     */
    public function setPlayerB(Player $playerB): void
    {
        $this->playerB = $playerB;
    }

    /**
     * @return float
     */
    public function getScorePlayerA(): ?float
    {
        return $this->scorePlayerA;
    }

    /**
     * @param float $scorePlayerA
     */
    public function setScorePlayerA(float $scorePlayerA): void
    {
        $this->scorePlayerA = $scorePlayerA;
    }

    /**
     * @return float
     */
    public function getScorePlayerB(): ?float
    {
        return $this->scorePlayerB;
    }

    /**
     * @param float $scorePlayerB
     */
    public function setScorePlayerB(float $scorePlayerB): void
    {
        $this->scorePlayerB = $scorePlayerB;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getWinner()
    {
        if (null === $this->scorePlayerA ?? $this->scorePlayerB ?? null) {
            throw new \Exception('Missing result to get a winner');
        }

        $potentialWinners = [
            -1 => $this->playerB,
            0 => null,
            1 => $this->playerA,
        ];

        return $potentialWinners[$this->scorePlayerA <=> $this->scorePlayerB];
    }

    public function updateRatios()
    {
        $winner = $this->getWinner();

        $resultPlayerA = $this->playerA === $winner ? 1 : ($this->playerB === $winner ? 0 : 0.5);
        $resultPlayerB = $this->playerB === $winner ? 1 : ($this->playerA === $winner ? 0 : 0.5);

        $this->playerA->updateRatioAgainst($this->playerB, $resultPlayerA);
        $this->playerB->updateRatioAgainst($this->playerA, $resultPlayerB);
    }
}
