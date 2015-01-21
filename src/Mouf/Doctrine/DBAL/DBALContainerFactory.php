<?php
namespace Mouf\Doctrine\DBAL;

use Doctrine\DBAL\Connection;

use Mouf\Actions\InstallUtils;
use Mouf\MoufManager;
use Mouf\Mvc\Splash\Controllers\Controller;
use Mouf\Picotainer\Picotainer;
use Interop\Container\ContainerInterface;
use Mouf\Doctrine\DBAL\Controllers\DBALConnectionInstallController;

/**
 * The factory in charge of creating the container with instances for this package.
 */
class DBALContainerFactory {
	
	public static function factory(ContainerInterface $rootContainer) {
		return new Picotainer([
				// Let's create the install controller.
				"dbalconnectioninstall" => function(ContainerInterface $container) {
					return new DBALConnectionInstallController(
							$container->get('moufInstallTemplate'), 
							$container->get('block.content'));
				}
		], $rootContainer);
	}
}
