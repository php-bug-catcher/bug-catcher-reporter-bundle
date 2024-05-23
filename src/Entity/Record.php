<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 22. 5. 2024
 * Time: 16:45
 */
namespace BugCatcher\Reporter\Entity;

use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

abstract class Record {

	#[ORM\Id]
	#[ORM\Column(type: UuidType::NAME, unique: true)]
	#[ORM\GeneratedValue(strategy: 'CUSTOM')]
	#[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
	protected ?Uuid $id = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	protected ?DateTimeInterface $date = null;

	#[ORM\ManyToOne()]
	#[ORM\JoinColumn(nullable: false)]
	protected ?Project $project = null;

	public function __construct(?Project $project, ?DateTimeInterface $date) {
		$this->date    = $date;
		$this->project = $project;
	}


}