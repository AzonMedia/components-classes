<?php
declare(strict_types=1);

namespace GuzabaPlatform\Classes\Models;


use Guzaba2\Authorization\Acl\Permission;
use Guzaba2\Base\Base;
use GuzabaPlatform\Platform\Application\MysqlConnectionCoroutine;

class Permissions extends Base
{

    protected const CONFIG_DEFAULTS = [
        'services'      => [
            'ConnectionFactory'
        ],
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * Returns the permissions of the controllers.
     * @param string $class_name
     * @param string $action_name
     * @return mixed
     * @throws \Guzaba2\Base\Exceptions\RunTimeException
     */
    //public static function get_permissions(string $class_name, string $action_name)
    public static function get_permissions(string $class_name) : iterable
    {
        $Connection = self::get_service('ConnectionFactory')->get_connection(MysqlConnectionCoroutine::class, $ScopeReference);

        $q = "
SELECT
    roles.*,
    meta.meta_object_uuid,
    acl_permissions.action_name
FROM
    {$Connection::get_tprefix()}roles as roles
LEFT JOIN
    {$Connection::get_tprefix()}acl_permissions as acl_permissions
    ON
        roles.role_id = acl_permissions.role_id
    AND
        acl_permissions.class_name = :class_name
    AND
        acl_permissions.object_id IS NULL
LEFT JOIN
    {$Connection::get_tprefix()}users as users
    ON
        roles.role_id = users.user_id
LEFT JOIN
    {$Connection::get_tprefix()}object_meta as meta
    ON
        meta.meta_object_id = acl_permissions.permission_id
    AND
        meta.meta_class_name = :meta_class_name
-- WHERE
--    (users.user_id IS NULL OR users.user_id = 1)
ORDER BY
    roles.role_name
";

        $b = ['class_name' => $class_name, 'meta_class_name' => Permission::class];

        $data = $Connection->prepare($q)->execute($b)->fetchAll();
//print $q.PHP_EOL;
//print_r($b);
//print_r($data);
        $ret = [];
        foreach ($data as $row) {

            $ret[$row['role_id']]['role_id'] = $row['role_id'];
            $ret[$row['role_id']]['role_name'] = $row['role_name'];

            if ($row['action_name']) {
                $ret[$row['role_id']][$row['action_name'] . '_granted'] = $row['meta_object_uuid'];
            }
        }

        return $ret;

//        $q = "
//SELECT
//    roles.*,
//    meta.meta_object_uuid,
//    CASE WHEN roles.role_id = acl_permissions.role_id THEN 1 ELSE 0 END as granted,
//    CASE WHEN roles.role_id = acl_permissions.role_id THEN 'success' ELSE '' END as _rowVariant
//FROM
//    {$Connection::get_tprefix()}roles as roles
//LEFT JOIN
//    {$Connection::get_tprefix()}acl_permissions as acl_permissions
//    ON
//        roles.role_id = acl_permissions.role_id
//    AND
//        acl_permissions.class_name = :class_name
//    AND
//        acl_permissions.action_name = :action_name
//    AND
//		(acl_permissions.object_id IS NULL OR acl_permissions.object_id = 0)
//LEFT JOIN
//    {$Connection::get_tprefix()}users as users
//    ON
//        roles.role_id = users.user_id
//LEFT JOIN
//    {$Connection::get_tprefix()}object_meta as meta
//    ON
//        meta.meta_object_id = acl_permissions.permission_id
//WHERE
//	(users.user_id IS NULL OR users.user_id = 1)
//
//ORDER BY
//    roles.role_name
//";
//
//        print $q.PHP_EOL;
//        print $class_name.PHP_EOL;
//        print $action_name.PHP_EOL;
//
//        $data = $Connection->prepare($q)->execute(['class_name' => $class_name, 'action_name' => $action_name])->fetchAll();
//        print_r($data);
//
    }
}