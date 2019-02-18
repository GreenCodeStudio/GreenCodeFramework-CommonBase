<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 16.02.2019
 * Time: 13:20
 */

namespace Common;


use Core\Migration;

class CodeGenerator
{
    function generate($namespace, $name)
    {
        $table = $this->getTable($name);
        dump($table);
        $path = __DIR__.'/../'.$namespace;

        if (!file_exists($path.'/Views')) {
            mkdir($path.'/Views', 0777, true);
        }
        if (!file_exists($path.'/Ajax')) {
            mkdir($path.'/Ajax', 0777, true);
        }
        if (!file_exists($path.'/Controllers')) {
            mkdir($path.'/Controllers', 0777, true);
        }
        if (!file_exists($path.'/DB')) {
            mkdir($path.'/DB', 0777, true);
        }

        if (!file_exists($path.'/Views/'.$name.'List.php')) {
            file_put_contents($path.'/Views/'.$name.'List.php', $this->makeViewList($namespace, $name, $table));
        }
        if (!file_exists($path.'/Views/'.$name.'Edit.php')) {
            file_put_contents($path.'/Views/'.$name.'Edit.php', $this->makeViewEdit($namespace, $name, $table));
        }
        if (!file_exists($path.'/Controllers/'.$name.'.php')) {
            file_put_contents($path.'/Controllers/'.$name.'.php', $this->makeController($namespace, $name, $table));
        }
        if (!file_exists($path.'/Ajax/'.$name.'.php')) {
            file_put_contents($path.'/Ajax/'.$name.'.php', $this->makeAjax($namespace, $name, $table));
        }
        if (!file_exists($path.'/'.$name.'Edit.php')) {
            file_put_contents($path.'/'.$name.'.php', $this->makeLogicModel($namespace, $name, $table));
        }
        if (!file_exists($path.'/DB/'.$name.'Edit.php')) {
            file_put_contents($path.'/DB/'.$name.'DB.php', $this->makeDBModel($namespace, $name, $table));
        }
        if (!file_exists($path.'/permissions.xml')) {
            file_put_contents($path.'/permissions.xml', '<?xml version="1.0" encoding="UTF-8"?><permissions/>');
        }
        $this->updatePermissions($path, $name);
    }

    function getTable($name)
    {
        $migration = new Migration();
        return $migration->readNewStructure()[$name];
    }

    function makeViewList(string $namespace, string $name, $table)
    {
        $cols = '';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            $cols .= '<th data-value="'.$column->name.'">'.$column->name.'</th>';
        }
        return '<div class="topBarButtons">
    <a href="/'.$name.'/add" class="button">Dodaj</a>
</div>
<section class="card" data-width="6">
    <header>
        <h1>Wszyscy użytkownicy</h1>
    </header>
    <div class="dataTableContainer">
        <table class="dataTable" data-controller="'.$name.'" data-method="getTable">
            <thead>
            <tr>
                '.$cols.'
                <th class="tableActions">Akcje
                    <div class="tableCopy">
                        <a href="/'.$name.'/edit" class="button">Edytuj</a>
                    </div>
                </th>
            </tr>
            </thead>
        </table>
    </div>
</section>';
    }

    function makeViewEdit(string $namespace, string $name, $table)
    {
        $form = '';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            $form .= '<label>
            <span>'.$column->name.'</span>
            '.$this->generateInput($column).'
        </label>';
        }
        return '<form class="dataForm" data-name="'.$name.'" data-controller="'.$name.'"
      data-method="<?= $data[\'type\'] == \'edit\' ? \'update\' : \'insert\' ?>" data-goto="/'.$name.'">
    <div class="topBarButtons">
        <button class="button" type="button">Anuluj</button>
        <button class="button">Zapisz</button>
    </div>
    <input name="id" type="hidden">
    <section class="card" data-width="6">
        <header>
            <h1>'.$name.'</h1>
        </header>
      '.$form.'
    </section>
</form>';
    }

    function generateInput($col)
    {
        switch ($col->type->__toString()) {
            default:
                return '<input type="text" name="'.$col->name.'">';
        }
    }

    function makeController(string $namespace, string $name, $table)
    {
        return '<?php

namespace '.$namespace.'\Controllers;

use Authorization\Permissions;
use Core\Exceptions\NotFoundException;

class '.$name.' extends \Common\PageStandardController
{

    function index()
    {
        $this->will(\''.$name.'\', \'show\');
        $this->addView(\''.$namespace.'\', \''.$name.'List\');
        $this->pushBreadcrumb([\'title\' => \''.$name.'\', \'url\' => \'/'.$name.'\']);

    }

    /**
     * @param int $id
     * @OfflineDataOnly
     */
    function edit(int $id)
    {
        $this->will(\''.$name.'\', \'edit\');
        $this->addView(\''.$namespace.'\', \''.$name.'Edit\', [\'type\' => \'edit\']);
        $this->pushBreadcrumb([\'title\' => \''.$name.'\', \'url\' => \'/'.$name.'\']);
        $this->pushBreadcrumb([\'title\' => \'Edycja\', \'url\' => \'/'.$name.'/edit/\'.$id]);
    }

    function edit_data(int $id)
    {
        $this->will(\''.$name.'\', \'edit\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        $data = $'.$name.'->getById($id);
        if ($data == null)
            throw new NotFoundException();
        return [\''.$name.'\' => $data];
    }

    /**
     * @OfflineConstant
     */
    function add()
    {
        $this->will(\''.$name.'\', \'add\');
        $permissionsStructure = Permissions::readStructure();
        $this->addView(\''.$namespace.'\', \''.$name.'Edit\', [\'type\' => \'add\']);
        $this->pushBreadcrumb([\'title\' => \''.$name.'\', \'url\' => \'/'.$name.'\']);
        $this->pushBreadcrumb([\'title\' => \'Dodaj\', \'url\' => \'/'.$name.'/add\']);
    }
}
';
    }

    function makeAjax(string $namespace, string $name, $table)
    {
        return '<?php
namespace '.$namespace.'\Ajax;

class '.$name.' extends \Core\AjaxController
{
    public function getTable($options)
    {
        $this->will(\''.$name.'\', \'show\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        return $'.$name.'->getDataTable($options);
    }

    public function update($data)
    {
        $this->will(\''.$name.'\', \'edit\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        $'.$name.'->update($data->id, $data);
    }

    public function insert($data)
    {
        $this->will(\''.$name.'\', \'add\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        $id = $'.$name.'->insert($data);
    }
}';
    }

    function makeLogicModel(string $namespace, string $name, $table)
    {
        $prep = '';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            $prep .= '$ret[\''.$column->name.'\'] = $data->'.$column->name.';'."\r\n";
        }

        return '<?php
namespace '.$namespace.';

use '.$namespace.'\DB\\'.$name.'DB;

class '.$name.' extends \Core\LogicModel
{
    public function __construct()
    {
        $this->defaultDB = new '.$name.'DB();
    }

    public function getDataTable($options)
    {
        return $this->defaultDB->getDataTable($options);
    }

    public function update(int $id, $data)
    {
        $filtered = $this->filterData($data);
        $this->defaultDB->update($id, $filtered);
    }

    protected function filterData($data)
    {
        $ret = [];
        '.$prep.'
        return $ret;
    }

    public function insert($data)
    {
        $filtered = $this->filterData($data);
        $id = $this->defaultDB->insert($filtered);
    }
}';
    }

    function makeDBModel(string $namespace, string $name, $table)
    {
        return '<?php

namespace '.$namespace.'\DB;

use Core\DB;


class '.$name.'DB extends \Core\DBModel
{

    public function __construct()
    {
        parent::__construct(\''.$name.'\');
        $this->archiveMode = static::ArchiveMode_OnlyExisting;
    }
       public function getDataTable($options)
    {
        $start = (int)$options->start;
        $limit = (int)$options->limit;
        $rows = DB::get("SELECT * FROM '.$name.' LIMIT $start,$limit");
        return [\'rows\' => $rows];
    }
}';
    }

    function updatePermissions($path, $name)
    {
        $filename=$path.'/permissions.xml';
        $xml = simplexml_load_string(file_get_contents($filename));
        $group=$xml->addChild('group');
        $group->name=$name;
        $group->title=$name;
        $group->permission[0]->name='show';
        $group->permission[0]->title='Odczyt';
        $group->permission[1]->name='edit';
        $group->permission[1]->title='edycja';
        $group->permission[2]->name='add';
        $group->permission[2]->title='Dodawanie';
        $group->permission[3]->name='remove';
        $group->permission[3]->title='Usuwanie';
        file_put_contents($filename, $xml->asXML());
    }
}