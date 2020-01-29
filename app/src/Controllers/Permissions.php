<?php
declare(strict_types=1);

namespace GuzabaPlatform\Classes\Controllers;


use Guzaba2\Http\Method;
use GuzabaPlatform\Platform\Application\BaseController;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Permissions
 * @package GuzabaPlatform\Classes\Controllers
 * Returns the permissions of the chosen class.
 */
class Permissions extends BaseController
{

    protected const CONFIG_DEFAULTS = [
        'routes'        => [
            //'/admin/permissions-classes/{class_name}/{method_name}' => [
            '/admin/permissions-classes/{class_name}' => [
                Method::HTTP_GET    => [self::class, 'main']
            ],
        ],
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * Returns the permissions of a method of a class.
     * A permission to execute the given method on a class means this can be executed on ony object from this class.
     * @param string $method_name
     */
    //public function main(string $method_name): ResponseInterface
    //public function main(string $class_name, string $method_name): ResponseInterface
    public function main(string $class_name): ResponseInterface
    {
        $struct = [];

        //list($class_name, $action_name) = explode("::", $method_name);
        //$class_name = str_replace(".", "\\", $class_name);
        if (strpos($class_name,'-') !== FALSE) {
            $class_name = str_replace('-', '\\', $class_name);
        }

        //$struct['items'] = \GuzabaPlatform\Platform\Crud\Models\Permissions::get_permissions($class_name, $action_name);
        //$struct['items'] = \GuzabaPlatform\Classes\Models\Permissions::get_permissions($class_name, $method_name);
        $struct['items'] = \GuzabaPlatform\Classes\Models\Permissions::get_permissions($class_name);

        $Response = parent::get_structured_ok_response($struct);
        return $Response;
    }
}