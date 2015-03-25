# Thelia 2 Routes Match Inverter

Allows to match the routes in reverse modules order

## Installation

### Manually…

Copy the module into `<thelia_root>/local/modules/` directory and be sure that the name of the module is `RoutesMatchInverter`.

### … or with Composer

Add it in your main thelia `composer.json` file

```
composer require bilhackmac/routes-match-inverter-module:~1.0
```
### Then

Activate it in your thelia administration panel.

## Explanation

In Thelia 2 you can overwrite any module loop/form/service by configuring in an other module an loop/form/service with the same id and by setting this module under (with greater position) the other in modules list. But it's not working in this way for routes

Consider module A, B and C configuration files :

_ModuleA/Config/config.xml_

```xml
<services>
    <service id="my.super.service" class="ModuleA\Handler\ModuleAHandler" />
</services>
```
_ModuleA/Config/routing.xml_

```xml
<route id="my.super.route" path="/my/super/route" methods="get">
    <default key="_controller">ModuleA:Main:index</default>
</route>
```

_ModuleB/Config/config.xml_

```xml
<services>
    <service id="my.super.service" class="ModuleB\Handler\ModuleAHandler" />
</services>
```
_ModuleB/Config/routing.xml_

```xml
<route id="my.super.route" path="/my/super/route" methods="get">
    <default key="_controller">ModuleB:Main:index</default>
</route>
```

_ModuleC/Config/config.xml_

```xml
<services>
    <service id="my.super.service" class="ModuleC\Handler\ModuleAHandler" />
</services>
```
_ModuleC/Config/routing.xml_

```xml
<route id="my.super.route" path="/my/super/route" methods="get">
    <default key="_controller">ModuleC:Main:index</default>
</route>
```

Now, in any controller, if you call `$this->container->get('my.super.service')`, you always get the **most** overridden module service (by greatest module position) **BUT**, in your browser/RESTClient, if you call `/my/super/route`, you always match the **less** (first matching) overridden module route.

A small table can be clearer :
| Module position | Service from module | Called controller (from route configuration) |
|:---:|:---:|:---:|
| `A > B > C` | `C` | `ModuleA:Main:index` |
| `A > C > B` | `B` | `ModuleA:Main:index` |
| `B > C > A` | `A` | `ModuleB:Main:index` |
| `C > B > A` | `A` | `ModuleC:Main:index` |
| `C > A > B` | `B` | `ModuleC:Main:index` |

A way to prevent this behaviour is to define your own router ([Thelia doc](http://doc.thelia.net/en/documentation/modules/routing.html#custom-routing)) but it can be boring or just a third module on which you do not have the hand.

So this module reverse the way that routes match for modules without defined router, and this is where the magic happens, previous table become :
| Module position | Service from module | Called controller (from route configuration) |
|:---:|:---:|:---:|
| `A > B > C` | `C` | `ModuleC:Main:index` |
| `A > C > B` | `B` | `ModuleB:Main:index` |
| `B > C > A` | `A` | `ModuleA:Main:index` |
| `C > B > A` | `A` | `ModuleA:Main:index` |
| `C > A > B` | `B` | `ModuleB:Main:index` |
