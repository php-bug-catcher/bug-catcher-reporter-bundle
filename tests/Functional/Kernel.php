<?php
/**
 * Created by PhpStorm.
 * User: Jozef Môstka
 * Date: 23. 5. 2024
 * Time: 14:14
 */
namespace BugCatcher\Reporter\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel {
	use MicroKernelTrait;


	private array $configFiles = [];

	public function __construct(private $options) {
		parent::__construct('test', true);
		$this->addConfigFile(__DIR__ . '/config.yaml');
	}

	public function addConfigFile(string $configFile): void {
		$this->configFiles[] = $configFile;
	}


	public function getCacheDir(): string {
		return $this->getProjectDir() . '/var/cache/' . $this->environment . '/' . spl_object_hash($this);
	}

	public function registerContainerConfiguration(LoaderInterface $loader): void {
		$loader->load(function (ContainerBuilder $container) use ($loader) {
			foreach ($this->configFiles as $path) {
				$loader->load($path);
			}
			$container->addObjectResource($this);
			$container->loadFromExtension('bug_catcher', $this->options);
		});
	}


}