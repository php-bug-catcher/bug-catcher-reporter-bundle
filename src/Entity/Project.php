<?php

namespace BugCatcher\Reporter\Entity;

use App\Repository\ProjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity()]
class Project {
	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
	private ?Uuid $id = null;

	#[ORM\Column(length: 255)]
	private ?string $code = null;
	#[ORM\Column(length: 255)]
	private ?string $name = "null";

	public function __construct() {}

	public function getId(): ?Uuid {
		return $this->id;
	}

	public function getCode(): ?string {
		return $this->code;
	}

	public function setCode(?string $code): self {
		$this->code = $code;

		return $this;
	}

}
