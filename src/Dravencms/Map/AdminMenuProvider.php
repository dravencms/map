<?php declare(strict_types=1);

namespace Dravencms\Map;

use Dravencms\Admin\Menu\{AdminMenuItem, AdminMenuProviderInterface};

final class AdminMenuProvider implements AdminMenuProviderInterface
{
    public function getItems(): iterable
    {
        yield new AdminMenuItem('map.overview', 'map.menu.overview', destination: ':Admin:Map:Map:default', parent: 'content', icon: 'fa-map', order: 60, resource: 'map', privilege: 'edit');
    }
}
