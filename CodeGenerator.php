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
    }

    function getTable($name)
    {
        $migration = new Migration();
        return $migration->readNewStructure()[$name];
    }

    function makeViewList(string $namespace, string $name, $table)
    {
        $cols='';
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
        return 'a';
    }

    function makeAjax(string $namespace, string $name, $table)
    {
        return 'a';
    }

    function makeLogicModel(string $namespace, string $name, $table)
    {
        return 'a';
    }

    function makeDBModel(string $namespace, string $name, $table)
    {
        return 'a';
    }
}