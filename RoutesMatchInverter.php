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

namespace RoutesMatchInverter;

use RoutesMatchInverter\DependencyInjection\Compiler\ChainRouterInverterPass;
use Thelia\Module\BaseModule;

/**
 * Class RoutesMatchInverter
 *
 * @author Jérôme Billiras <jerome DOT billiras PLUS github AT gmail DOT com>
 */
class RoutesMatchInverter extends BaseModule
{
    /**
     * @inheritdoc
     */
    public static function getCompilers()
    {
        return [
            new ChainRouterInverterPass
        ];
    }
}
