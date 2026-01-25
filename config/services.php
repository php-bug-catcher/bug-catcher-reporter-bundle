<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BugCatcher\Reporter\Event\WriteStackTraceListener;
use BugCatcher\Reporter\Service\BugCatcher;
use BugCatcher\Reporter\Service\BugCatcherInterface;
use BugCatcher\Reporter\Service\BugCatcherMonologHandler;
use BugCatcher\Reporter\UrlCatcher\ConsoleUriCatcher;
use BugCatcher\Reporter\UrlCatcher\HttpUriCatcher;
use BugCatcher\Reporter\Writer\HttpWriter;

return static function (ContainerConfigurator $container): void {
	$services = $container->services();

	$services->set('bug_catcher', BugCatcher::class)
		->public(false);

	$services->set('bug_catcher.handler', BugCatcherMonologHandler::class)
		->public(true);

	$services->alias(BugCatcherMonologHandler::class, 'bug_catcher.handler')
		->public(false);

	$services->alias('bug_catcher.writer', 'bug_catcher.writer.http_writer');

	$services->alias(BugCatcherInterface::class, 'bug_catcher')
		->public(true);

	$services->set('bug_catcher.writer.http_writer', HttpWriter::class)
		->public(true);

	$services->set('bug_catcher.uri_catcher.http_catcher', HttpUriCatcher::class)
		->public(true)
		->args([
			service('request_stack'),
		]);

	$services->set(WriteStackTraceListener::class)
		->tag('kernel.event_listener');

	$services->set('bug_catcher.uri_catcher.console_catcher', ConsoleUriCatcher::class)
		->public(true);
};
