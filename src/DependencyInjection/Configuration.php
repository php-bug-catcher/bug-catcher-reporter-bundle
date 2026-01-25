<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 12:48
 */
namespace BugCatcher\Reporter\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

	public function getConfigTreeBuilder(): TreeBuilder {
		$treeBuilder = new TreeBuilder('bug_catcher');
		$rootNode    = $treeBuilder->getRootNode();
		$rootNode
			->children()
			->scalarNode("project")->isRequired()->end()
			->integerNode("min_level")->defaultValue(500)->end()
			->scalarNode("http_client")->defaultNull()->end()
//			->scalarNode("connection")->defaultValue("default")->end()
			->scalarNode("uri_catcher")->defaultNull()->end()
			->booleanNode("stack_trace")->defaultTrue()->end()
			->scalarNode("writer")->defaultNull()->end()
			->end();

		return $treeBuilder;
	}
}