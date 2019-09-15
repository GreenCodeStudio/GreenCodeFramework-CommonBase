<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 16.02.2019
 * Time: 13:20
 */

namespace CommonBase;


use Core\Migration;

class CodeGenerator
{
    function generate($namespace, $name, $dbName)
    {
        $table = $this->getTable($name);
        if ($table->title)
            $title = htmlspecialchars($table->title);
        else
            $title = $name;
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
        if (!file_exists($path.'/Repository')) {
            mkdir($path.'/Repository', 0777, true);
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
        if (!file_exists($path.'/'.$name.'.php')) {
            file_put_contents($path.'/'.$name.'.php', $this->makeBussinesLogic($namespace, $name, $table));
        }
        if (!file_exists($path.'/Repository/'.$name.'Repository.php')) {
            file_put_contents($path.'/Repository/'.$name.'Repository.php', $this->makeRepository($namespace, $name,$dbName, $table));
        }
        if (!file_exists($path.'/permissions.xml')) {
            file_put_contents($path.'/permissions.xml', '<?xml version="1.0" encoding="UTF-8"?><permissions/>');
        }
        $this->updatePermissions($path, $name, $title);
        if (!file_exists($path.'/menu.xml')) {
            file_put_contents($path.'/menu.xml', '<?xml version="1.0" encoding="UTF-8"?><menu/>');
        }
        $this->updateMenu($path, $name, $title);
    }

    function getTable($name)
    {
        $migration = Migration::factory();
        return $migration->readNewStructure()[$name];
    }

    function makeViewList(string $namespace, string $name, $table)
    {
        $cols = '';
        if ($table->title)
            $title = htmlspecialchars($table->title);
        else
            $title = $name;
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            if ($column->title)
                $columnTitle = htmlspecialchars($column->title);
            else
                $columnTitle = $column->name;
            $cols .= '<th data-value="'.$column->name.'" data-sortable>'.$columnTitle.'</th>';
        }
        return '<div class="topBarButtons">
    <a href="/'.$name.'/add" class="button">Dodaj</a>
</div>
<section class="card" data-width="6">
    <header>
        <h1>Lista elementów typu '.$title.'</h1>
    </header>
    <div class="dataTableContainer">
        <table class="dataTable" data-controller="'.$name.'" data-method="getTable" data-web-socket-path="'.$namespace.'/'.$name.'">
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

        if ($table->title)
            $title = htmlspecialchars($table->title);
        else
            $title = $name;
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            $reference = null;
            foreach ($table->index as $index) {
                if ($index->type == 'FOREIGN' && $index->element->__toString() == $column->name->__toString()) {
                    $reference = $index->reference;
                }
            }
            if ($column->title)
                $columnTitle = htmlspecialchars($column->title);
            else
                $columnTitle = $column->name;
            $form .= '<label>
            <span>'.$columnTitle.'</span>
            '.$this->generateInput($column, $reference).'
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
            <h1>'.$title.'</h1>
        </header>
      '.$form.'
    </section>
</form>';
    }

    function generateInput($col, $reference = null)
    {
        $required = (strtolower($col->null) != 'yes');
        if (!empty($reference)) {
            return '<select data-foreign-key="'.$reference->attributes()->name.'" name="'.$col->name.'" '.($required ? 'required' : '').'></select>';
        } else {
            switch ($col->type->__toString()) {
                case "bool":
                    return '<input type="checkbox" name="'.$col->name.'">';
                case "int":
                    return '<input type="number" step="1" name="'.$col->name.'" '.($required ? 'required' : '').'>';
                case "date":
                    return '<input type="date" name="'.$col->name.'" '.($required ? 'required' : '').'>';
                case "datetime":
                    return '<input type="datetime-local" step="any" name="'.$col->name.'" '.($required ? 'required' : '').'>';
                default:
                    return '<input type="text" name="'.$col->name.'" '.($required ? 'required' : '').'>';
            }
        }
    }

    function makeController(string $namespace, string $name, $table)
    {

        $add_data = '';
        $hasForeignKeys = false;
        foreach ($table->index as $index) {
            dump($index->type);
            if ($index->type == 'FOREIGN') {
                $hasForeignKeys = true;
                break;
            }
        }
        if ($hasForeignKeys) {
            $add_data = 'function add_data()
    {
        $this->will(\''.$name.'\', \'add\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        return [\'selects\' => $'.$name.'->getSelects()];
    }';
        }

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
        return [\''.$name.'\' => $data'.($hasForeignKeys ? ',\'selects\'=>$'.$name.'->getSelects()' : '').'];
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
    '.$add_data.'
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

    function makeBussinesLogic(string $namespace, string $name, $table)
    {
        $prep = '';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            if ($column->type == 'bool')
                $prep .= '$ret[\''.$column->name.'\'] = (int)isset($data->'.$column->name.')';
            else if (strtolower($column->null) == 'yes')
                $prep .= '$ret[\''.$column->name.'\'] = empty($data->'.$column->name.')?null:$data->'.$column->name;
            else
                $prep .= '$ret[\''.$column->name.'\'] = $data->'.$column->name;
            $prep .= ';'."\r\n";
        }
        $referencesMethod = '';
        $referencesCode = [];
        foreach ($table->index as $index) {
            if ($index->type == 'FOREIGN') {
                $referencesCode[] = '        $'.$index->reference->attributes()->name.' = new Repository\\'.$index->reference->attributes()->name.'Repository();
        $ret["'.$index->reference->attributes()->name.'"] = $'.$index->reference->attributes()->name.'->getSelect();';
            }
        }
        if (!empty($referencesCode)) {
            $referencesMethod = 'public function getSelects(){
        $ret=[];
        '.implode("\r\n", $referencesCode).'
        return $ret;
    }';
        }
        return '<?php
namespace '.$namespace.';

use '.$namespace.'\Repository\\'.$name.'Repository;

class '.$name.' extends \Core\BussinesLogic
{
    public function __construct()
    {
        $this->defaultDB = new '.$name.'Repository();
    }

    public function getDataTable($options)
    {
        return $this->defaultDB->getDataTable($options);
    }

    public function update(int $id, $data)
    {
        $filtered = $this->filterData($data);
        $this->defaultDB->update($id, $filtered);
        \Core\WebSocket\Sender::sendToUsers(["'.$namespace.'", "'.$name.'", "Update", $id]);
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
        \Core\WebSocket\Sender::sendToUsers(["'.$namespace.'", "'.$name.'", "Insert", $id]);
    }
    '.$referencesMethod.'
}';
    }

    function makeRepository(string $namespace, string $name,string $dbName,  $table)
    {
        $orderCodes = [];
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            $orderCodes[] = '\''.$column->name.'\'=> \''.$column->name.'\'';
        }
        $orderCode = implode(', ', $orderCodes);
        return '<?php

namespace '.$namespace.'\Repository;

use Core\DB;


class '.$name.'Repository extends \Core\Repository
{

    public function __construct()
    {
        $this->archiveMode = static::ArchiveMode_OnlyExisting;
    }
    public function defaultTable(): string
    {
        return \''.$dbName.'\';
    }
    public function getDataTable($options)
    {
        $start = (int)$options->start;
        $limit = (int)$options->limit;
        $sqlOrder = $this->getOrderSQL($options);
        $rows = DB::get("SELECT * FROM '.$dbName.' $sqlOrder LIMIT $start,$limit");
        $total = DB::get("SELECT count(*) as count FROM '.$dbName.'")[0]->count;
        return [\'rows\' => $rows, \'total\' => $total];
    }
        private function getOrderSQL($options)
    {
        if (empty($options->sort))
            return "";
        else {
            $mapping = ['.$orderCode.'];
            if (empty($mapping[$options->sort->col]))
                throw new Exception();
            return \' ORDER BY \'.DB::safeKey($mapping[$options->sort->col]).\' \'.($options->sort->desc ? \'DESC\' : \'ASC\').\' \';
        }
    }
}';
    }

    function updatePermissions($path, $name, $title)
    {
        $filename = $path.'/permissions.xml';
        $xml = simplexml_load_string(file_get_contents($filename));
        $group = $xml->addChild('group');
        $group->name = $name;
        $group->title = $title;
        $group->permission[0]->name = 'show';
        $group->permission[0]->title = 'Odczyt';
        $group->permission[1]->name = 'edit';
        $group->permission[1]->title = 'edycja';
        $group->permission[2]->name = 'add';
        $group->permission[2]->title = 'Dodawanie';
        $group->permission[3]->name = 'remove';
        $group->permission[3]->title = 'Usuwanie';
        file_put_contents($filename, $xml->asXML());
    }

    function updateMenu($path, $name, $title)
    {
        $filename = $path.'/menu.xml';
        $xml = simplexml_load_string(file_get_contents($filename));
        $group = $xml->addChild('element');
        $group->link = '/'.$name;
        $group->title = $title;
        file_put_contents($filename, $xml->asXML());
    }
}