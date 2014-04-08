<?php
use Mouf\MoufManager;
// Controller declaration
MoufManager::getMoufManager()->declareComponent('dbalconnectionedit', 'Mouf\\Doctrine\\DBAL\\Controllers\\DBALConnectionEditController', true);
MoufManager::getMoufManager()->bindComponents('dbalconnectionedit', 'template', 'moufTemplate');

MoufManager::getMoufManager()->declareComponent('dbalconnectioninstall', 'Mouf\\Doctrine\\DBAL\\Controllers\\DBALConnectionInstallController', true);
MoufManager::getMoufManager()->bindComponents('dbalconnectioninstall', 'template', 'moufInstallTemplate');
MoufManager::getMoufManager()->bindComponents('dbalconnectioninstall', 'contentBlock', 'block.content');
?>