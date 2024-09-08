<?php

declare(strict_types=1);

namespace Sys\framework\Database\Interface;

/**
 * Database ForgeInterface Interface
 *
 * @category Database
 * @package Sys\framework\Database
 * @author 
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link 
 * @version 0.0.1
 */

interface ForgeInterface
{
    public function create($schema);

    public function alter($schema);

    public function up();

    public function down();
}
