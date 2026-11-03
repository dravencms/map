<?php declare(strict_types=1);

namespace Dravencms\Map;

use Dravencms\User\Acl\{AclPermission, AclProviderInterface};

final class AclProvider implements AclProviderInterface
{
    public function getPermissions(): iterable
    {
        yield new AclPermission('map', 'edit', 'map.acl.permissions.edit.label', resourceLabel: 'map.acl.resource');
        yield new AclPermission('map', 'delete', 'map.acl.permissions.delete.label', resourceLabel: 'map.acl.resource');
    }
}
