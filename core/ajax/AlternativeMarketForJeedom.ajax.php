<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

namespace NextDom\Amfj\ajax;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../../../core/php/core.inc.php';

use NextDom\Amfj\AmfjAjaxParser;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

\header('Content-Type: application/json');

ErrorHandler::register();
ExceptionHandler::register();

try {
    \include_file('core', 'authentification', 'php');

    if (!\isConnect('admin')) {
        throw new \Exception(__('401 - Accès non autorisé', __FILE__));
    }

    \ajax::init();

    $action = \init('action');
    $params = \init('params');
    $data = \init('data');

    $result = AmfjAjaxParser::parse($action, $params, $data);

    if ($result !== false) {
        \ajax::success($result);
    }

    throw new \Exception(\__('Aucune méthode correspondante à : ', __FILE__) . \init('action'));
} catch (\Exception $e) {
    \ajax::error(\displayExeption($e), $e->getCode());
}

