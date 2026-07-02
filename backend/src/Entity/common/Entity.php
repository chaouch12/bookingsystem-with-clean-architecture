<?php

declare(strict_types=1);

namespace App\Entity\common;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(
        options: [
            'unsigned' => true,
        ]
    )]
    protected int $id;

    protected function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
