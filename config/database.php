<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

return [
    // 默认数据库
    'default'     => 'mysql',
    // 各种数据库配置
    'connections' => [

        'mysql' => [
            'driver'      => 'mysql',
            'host'        => getenv('MYSQL_HOST', '127.0.0.1'),
            'port'        => getenv('MYSQL_PORT', '3306'),
            'database'    => getenv('MYSQL_DATABASE', ''),
            'username'    => getenv('MYSQL_USER', 'root'),
            'password'    => getenv('MYSQL_PASSWORD', '123456'),
            'unix_socket' => '',
            'charset'     => getenv('MYSQL_CHARSET', 'utf8'),
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => getenv('MYSQL_PREFIX', ''),
            'strict'      => true,
            'engine'      => null,
        ],
    ]
];
