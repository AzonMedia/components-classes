<?php
declare(strict_types=1);

namespace GuzabaPlatform\Classes\Controllers;

use Guzaba2\Http\Method;
use GuzabaPlatform\Platform\Application\BaseController;
use Psr\Http\Message\ResponseInterface;

class Classes extends BaseController
{
    protected const CONFIG_DEFAULTS = [
        'routes'        => [
            '/admin/classes' => [
                Method::HTTP_GET => [self::class, 'main']
            ],
        ],
    ];

    protected const CONFIG_RUNTIME = [];

    /**
     * returns a list with all ActiveRecord classes
     */
    public function main(): ResponseInterface
    {
        $struct['tree'] = [];

        //$classes = \GuzabaPlatform\Platform\Crud\Models\Permissions::get_tree();

        $classes = \GuzabaPlatform\Classes\Models\Classes::get_tree();

//        $struct['tree'] = [
//            'Controllers' => $classes[0],
//            'Non-Controllers' => $classes[1]
//        ];
        $struct['tree'] = $classes;

        $Response = parent::get_structured_ok_response($struct);
        return $Response;
    }
}