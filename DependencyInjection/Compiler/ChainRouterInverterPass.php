<?php
/*************************************************************************************/
/*                                                                                   */
/*    RoutesMatchInverter                                                            */
/*    A Thelia 2 module to match the routes in reverse modules order                 */
/*    Copyright (C) 2015 Jérôme BILLIRAS                                             */
/*                                                                                   */
/*    This file is part of RoutesMatchInverter                                       */
/*                                                                                   */
/*    RoutesMatchInverter is free software: you can redistribute it and/or modify    */
/*    it under the terms of the GNU Lesser General Public License as published by    */
/*    the Free Software Foundation, either version 3 of the License, or              */
/*    any later version.                                                             */
/*                                                                                   */
/*    RoutesMatchInverter is distributed in the hope that it will be useful,         */
/*    but WITHOUT ANY WARRANTY; without even the implied warranty of                 */
/*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                  */
/*    GNU Lesser General Public License for more details.                            */
/*                                                                                   */
/*    You should have received a copy of the GNU Lesser General Public License       */
/*    along with this program. If not, see <http://www.gnu.org/licenses/>.           */
/*                                                                                   */
/*************************************************************************************/

namespace RoutesMatchInverter\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;
use Thelia\Model\ModuleQuery;

/**
 * Class ChainRouterInverterPass
 *
 * @author Jérôme Billiras <jerome DOT billiras PLUS github AT gmail DOT com>
 */
class ChainRouterInverterPass implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        try {
            $chainRouter = $container->getDefinition('router.chainRequest');

            $chainRouterOverride = new Definition($chainRouter->getClass());
            $chainRouterOverride->addMethodCall('setContext', [new Reference('request.context')]);
            $container->setDefinition('router.chainRequest', $chainRouterOverride);
        } catch (InvalidArgumentException $e) {
            return;
        }

        foreach ($container->findTaggedServiceIds('router.register') as $id => $attributes) {
            if (isset($attributes[0]['priority'])) {
                $priority = $attributes[0]['priority'];
            } else {
                $priority = 0;
            }
            $chainRouterOverride->addMethodCall('add', [new Reference($id), $priority]);
        }

        if (defined('THELIA_INSTALL_MODE') === false) {
            $modules = array_reverse(ModuleQuery::getActivated()->getArrayCopy());

            /** @var \Thelia\Model\Module $module */
            foreach ($modules as $module) {
                $moduleBaseDir = $module->getBaseDir();
                $routingConfigFilePath = $module->getAbsoluteBaseDir() . DS . 'Config' . DS . 'routing.xml';

                if (file_exists($routingConfigFilePath)) {
                    $chainRouterOverride->addMethodCall('add', [new Reference('router.' . $moduleBaseDir), 150]);
                }
            }
        }
    }
}
