<?php
require_once __DIR__ . '/../../../../core/php/core.inc.php';
require_once __DIR__ . '/../../vendor/autoload.php';

namespace NextDom\Amfj;

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

ErrorHandler::register();
ExceptionHandler::register();

/**
* Classe des objets de Jeedom
*/
class AlternativeMarketForJeedom extends \eqLogic
{

}
