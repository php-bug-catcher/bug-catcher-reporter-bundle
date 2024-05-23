<?php

namespace BugCatcher\Reporter\Entity;

use App\Repository\LogRecordRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Monolog\DateTimeImmutable;

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

	public function __construct(Project $project, string $message, int $level, ?string $requestUri) {
		$this->checked    = false;
		$this->message    = $message;
		$this->requestUri = $requestUri;
		$this->level      = $level;
		parent::__construct($project, new DateTimeImmutable());
	}


}
