<?php

namespace BugCatcher\Reporter\Entity;

use App\Repository\LogRecordRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity()]
class LogRecord extends Record {

	#[ORM\Column]
	private ?bool $checked = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $message = null;

	#[ORM\Column(length: 255)]
	private ?string $requestUri = null;

	#[ORM\Column]
	private ?int $level = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $stackTrace = null;

	public function __construct(Project $project, string $message, int $level, ?string $requestUri, ?string $stackTrace) {
		$this->checked    = false;
		$this->message    = $message;
		$this->requestUri = $requestUri;
		$this->level      = $level;
		$this->stackTrace = $stackTrace;
		parent::__construct($project, new DateTimeImmutable());
	}


}
