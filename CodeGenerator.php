<?php
/**
 * Created by PhpStorm.
 * User: matri
 * Date: 16.02.2019
 * Time: 13:20
 */

namespace CommonBase;


use Core\ApiController;
use Core\Database\DB;
use Core\Database\Migration;

class CodeGenerator
{
    protected $hiddenColumns = ['id', 'is_archived', 'created', 'updated'];

    function generate($namespace, $name, $dbName)
    {
        $table = $this->getTable($dbName);
        if ($table->title)
            $title = htmlspecialchars($table->title);
        else
            $title = $name;
        $path = __DIR__.'/../'.$namespace;

        if (!file_exists($path.'/Views')) {
            mkdir($path.'/Views', 0777, true);
        }
        if (!file_exists($path.'/Api')) {
            mkdir($path.'/Api', 0777, true);
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
        if (!file_exists($path.'/js')) {
            mkdir($path.'/js', 0777, true);
        }
        if (!file_exists($path.'/js/Controllers')) {
            mkdir($path.'/js/Controllers', 0777, true);
        }
        if (!file_exists($path.'/Test/Selenium')) {
            mkdir($path.'/Test/Selenium', 0777, true);
        }

        if (!file_exists($path.'/Views/'.$name.'List.php')) {
            file_put_contents($path.'/Views/'.$name.'List.mpts', $this->makeViewList($namespace, $name, $table));
        }
        if (!file_exists($path.'/Views/'.$name.'Edit.php')) {
            file_put_contents($path.'/Views/'.$name.'Edit.mpts', $this->makeViewEdit($namespace, $name, $table));
        }
        if (!file_exists($path.'/Controllers/'.$name.'Controller.php')) {
            file_put_contents($path.'/Controllers/'.$name.'Controller.php', $this->makeController($namespace, $name, $table));
        }
        if (!file_exists($path.'/Ajax/'.$name.'Ajax.php')) {
            file_put_contents($path.'/Ajax/'.$name.'Ajax.php', $this->makeAjax($namespace, $name, $table));
        }
        if (!file_exists($path.'/'.$name.'.php')) {
            file_put_contents($path.'/'.$name.'.php', $this->makeBussinesLogic($namespace, $name, $table));
        }
        if (!file_exists($path.'/Repository/'.$name.'Repository.php')) {
            file_put_contents($path.'/Repository/'.$name.'Repository.php', $this->makeRepository($namespace, $name, $dbName, $table));
        }
        if (!file_exists($path.'/Api/'.$name.'Api.php')) {
            file_put_contents($path.'/Api/'.$name.'Api.php', $this->makeApiController($namespace, $name, $table));
        }
        if (!file_exists($path.'/js/index.js')) {
            file_put_contents($path.'/js/index.js', 'import {pageManager} from "../../Core/js/pageManager";');
        }
        if (strpos(file_get_contents($path.'/js/index.js'), 'import(\'./Controllers/'.$name.'\'))') === false) {
            file_put_contents($path.'/js/index.js', "\r\n".'pageManager.registerController(\''.$name.'\', () => import(\'./Controllers/'.$name.'\'));', FILE_APPEND);
        }

        if (!file_exists($path.'/js/Controllers/'.$name.'.js')) {
            file_put_contents($path.'/js/Controllers/'.$name.'.js', $this->makeJsController($namespace, $name, $table));
        }
        if (!file_exists($path.'/permissions.xml')) {
            file_put_contents($path.'/permissions.xml', '<?xml version="1.0" encoding="UTF-8"?><permissions/>');
        }
        $this->updatePermissions($path, $name, $title);
        if (!file_exists($path.'/menu.xml')) {
            file_put_contents($path.'/menu.xml', '<?xml version="1.0" encoding="UTF-8"?><menu/>');
        }
        $this->updateMenu($path, $name, $title);
        if (!file_exists($path.'/i18n.xml')) {
            file_put_contents($path.'/i18n.xml', '<?xml version="1.0" encoding="UTF-8"?><root/>');
        }
        if (!file_exists($path.'/Test/Selenium/'.$name.'Test.js')) {
            file_put_contents($path.'/Test/Selenium/'.$name.'Test.js', $this->makeE2eTest($namespace, $name));
        }
        $this->updateI18n($path, $name, $table);
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

        return '<div class="topBarButtons">
    <a href="/'.$name.'/add" class="button"><span class="icon-add"></span> {{t("CommonBase.add")}}</a>
</div>
<div class="grid page-'.$name.'  page-'.$name.'-list">
    <section class="card" data-width="6">
        <header>
            <h1>{{t("'.$namespace.'.'.$name.'List.header")}}</h1>
        </header>
        <div class="container"></div>
    </section>
</div>';
    }

    function makeViewEdit(string $namespace, string $name, $table)
    {
        $form = '';
        $title = '{{t("'.$namespace.'.'.$name.'.'.$name.'")}}';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            if (in_array($column->name->__toString(), $this->hiddenColumns)) continue;
            $reference = null;
            foreach ($table->index as $index) {
                if ($index->type == 'FOREIGN' && $index->element->__toString() == $column->name->__toString()) {
                    $reference = $index->reference;
                }
            }
            $columnTitle = '{{t("'.$namespace.'.'.$name.'.'.$column->name.'")}}';
            $form .= '<label>
            <span>'.$columnTitle.'</span>
            '.$this->generateInput($column, $reference).'
        </label>';
        }
        return '<form>
    <div class="topBarButtons">
        <a href="/'.$name.'/" class="button"><span class="icon-cancel"></span>{{t("CommonBase.cancel")}}</a>
        <button class="button"><span class="icon-save"></span>{{t("CommonBase.save")}}</button>
    </div>
    <div class="grid page-'.$name.' page-'.$name.'-edit">
        <input name="id" type="hidden" />
        <section class="card" data-width="6">
            <header>
                <h1>'.$title.'</h1>
            </header>
          '.$form.'
        </section>
    </div>
</form>';
    }

    function generateInput($col, $reference = null)
    {
        $required = (strtolower($col->null) != 'yes');
        if (!empty($reference)) {
            return '<select data-foreign-key="'.$reference->attributes()->name.'" name="'.$col->name.'" '.($required ? 'required' : '').' > </select>';
        } else {
            switch ($col->type->__toString()) {
                case "bool":
                    return '<input type="checkbox" name="'.$col->name.'" />';
                case "int":
                    return '<input type="number" step="1" name="'.$col->name.'" '.($required ? 'required' : '').' />';
                case "date":
                    return '<input type="date" name="'.$col->name.'" '.($required ? 'required' : '').' />';
                case "datetime":
                    return '<input type="datetime-local" step="any" name="'.$col->name.'" '.($required ? 'required' : '').' />';
                default:
                    return '<input type="text" name="'.$col->name.'" '.($required ? 'required' : '').' />';
            }
        }
    }

    function makeController(string $namespace, string $name, $table)
    {

        $add_data = '';
        $hasForeignKeys = false;
        foreach ($table->index as $index) {
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
class '.$name.'Controller extends \Common\PageStandardController
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

class '.$name.'Ajax extends \Core\AjaxController
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
    
    public function updateMultiple(array $data)
    {      
        $this->will(\''.$name.'\', \'edit\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        foreach ($data as $row) {
            $'.$name.'->update($row->id, $row->data);
        }
    }

    public function insert($data)
    {
        $this->will(\''.$name.'\', \'add\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        $id = $'.$name.'->insert($data);
    }
}';
    }

    function makeApiController(string $namespace, string $name, $table)
    {
        $schemaOne = [
        ];
        foreach ($table->column as $column) {
            $schemaOne[$column->name.''] = [
                "type" => $column->type.'',
                "format" => $column->type == 'datetime' ? 'date-time' : null,
            ];
        }

        $getListDocs = [
            'type' => 'get',
            'url' => $name,
            'tags' => [$namespace.'-'.$name],
            'description' => 'Get list of '.$name,
            'responses' => [
                200 => [
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "array",
                                "items" => [
                                    "type" => "object",
                                    "properties" => $schemaOne
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $getOneDocs = [
            'type' => 'get',
            'url' => $name.'/{id}',
            'tags' => [$namespace.'-'.$name],
            'description' => 'Get one '.$name,
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true
                ]
            ],
            'responses' => [
                200 => [
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => $schemaOne
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $insertDocs = [
            'type' => 'post',
            'url' => $name,
            'tags' => [$namespace.'-'.$name],
            'description' => 'Insert one '.$name,
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => $schemaOne
                        ]
                    ]
                ]
            ],
            'responses' => [
                200 => [
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => $schemaOne
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $updateDocs = [
            'type' => 'put',
            'url' => $name.'/{id}',
            'tags' => [$namespace.'-'.$name],
            'description' => 'Insert one '.$name,
            'parameters' => [
                [
                    'name' => 'id',
                    'in' => 'path',
                    'required' => true
                ]
            ],
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => $schemaOne
                        ]
                    ]
                ]
            ],
            'responses' => [
                200 => [
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => $schemaOne
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return '<?php
namespace '.$namespace.'\Api;

class '.$name.'Api extends \Core\ApiController
{
/**
     * @ApiEndpoint('.str_replace("\n", "\n * ", substr(var_export($getListDocs, true), 7, -1)).')
     **/
    public function getList()
    {
        $this->will(\''.$name.'\', \'show\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        return $'.$name.'->getAll();
    }
    
    /**
     * @ApiEndpoint('.str_replace("\n", "\n * ", substr(var_export($getOneDocs, true), 7, -1)).')
     **/
    public function getOneById(int $id)
    {
        $this->will(\''.$name.'\', \'show\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        return $'.$name.'->getById($id);
    }
     /**
     * @ApiEndpoint('.str_replace("\n", "\n * ", substr(var_export($insertDocs, true), 7, -1)).')
     **/
    public function insert($data)
    {
        $this->will(\''.$name.'\', \'add\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        $id = $'.$name.'->insert($data);
        return $'.$name.'->getById($id);
    }
    
        /**
     * @ApiEndpoint('.str_replace("\n", "\n * ", substr(var_export($updateDocs, true), 7, -1)).')
     **/
    public function update($data, int $id)
    {
        $this->will(\''.$name.'\', \'edit\');
        $'.$name.' = new \\'.$namespace.'\\'.$name.'();
        $'.$name.'->update($id, $data);
        return $'.$name.'->getById($id);
    }
    

}';
    }

    function makeBussinesLogic(string $namespace, string $name, $table)
    {
        $prep = '';
        $createdCode = '';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            if ($column->name == 'is_archived') continue;
            if ($column->name == 'created') {
                $createdCode = '$filtered[\''.$column->name.'\'] = new \DateTime();';
                continue;
            }
            if ($column->name == 'updated')
                $prep .= '$ret[\''.$column->name.'\'] = new \DateTime();';
            else if ($column->type == 'bool')
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

    public function insert($data):int
    {
        $filtered = $this->filterData($data);
        '.$createdCode.'
        $id = $this->defaultDB->insert($filtered);
        \Core\WebSocket\Sender::sendToUsers(["'.$namespace.'", "'.$name.'", "Insert", $id]);
        return $id;
    }
    public function getAll()
    {
        return $this->defaultDB->getAll();
    }
    
    '.$referencesMethod.'
}';
    }

    function makeRepository(string $namespace, string $name, string $dbName, $table)
    {
        $orderCodes = [];
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            $orderCodes[] = '\''.$column->name.'\'=> \''.$column->name.'\'';
        }
        $orderCode = implode(', ', $orderCodes);
        return '<?php

namespace '.$namespace.'\Repository;

use Core\Database\DB;
use Exception;


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
    }    public function getAll()
    {
        if($this->archiveMode == static::ArchiveMode_OnlyExisting)
            return DB::get("SELECT * FROM '.$dbName.' WHERE is_archived = 0");
        else
            return DB::get("SELECT * FROM '.$dbName.'");
    }
}';
    }

    function makeJsController(string $namespace, string $name, $table)
    {
        $cols = '';
        foreach ($table->column as $column) {
            if ($column->autoincrement == 'YES') continue;
            if (in_array($column->name.'', $this->hiddenColumns)) continue;
            $cols .= '        objectsList.columns.push({
            name: t(\''.$name.'.'.$column->name.'\'),
            dataName: \''.$column->name.'\',
            sortName: \''.$column->name.'\',
            width: 100,
            widthGrow: 1
        });';
        }
        return 'import {FormManager} from "../../../Core/js/form";
import {Ajax} from "../../../Core/js/ajax";
import {pageManager} from "../../../Core/js/pageManager";
import {DatasourceAjax} from "../../../Core/js/datasourceAjax";
import {t} from "../../i18n.xml";
import {t as TCommonBase} from "../../../CommonBase/i18n.xml";
import {ObjectsList} from "../../../Core/js/ObjectsList/objectsList";
import {Permissions} from "../../../Core/js/permissions";

export class index {
    constructor(page, data) {
        const container = page.querySelector(\'.page-'.$name.'-list .container\');
        let datasource = new DatasourceAjax(\''.$name.'\', \'getTable\', [\''.$namespace.'\', \''.$name.'\'], null, \'updateMultiple\');
        let objectsList = new ObjectsList(datasource);
        objectsList.allowTableEdit = true;
        objectsList.columns = [];
        '.$cols.'
        objectsList.generateActions = (rows, mode) => {
            let ret = [];
            if (rows.length == 1) {
                if (Permissions.can(\''.$name.'\', \'edit\')) {
                    ret.push({
                        name: TCommonBase("edit"),
                        icon: \'icon-edit\',
                        href: "/'.$name.'/edit/" + rows[0].id,
                    });
                }
            }
            return ret;
        }
        container.append(objectsList);
        objectsList.refresh();
    }
}
export class edit {
    constructor(page, data) {
        let form = new FormManager(page.querySelector(\'form\'));
        form.loadSelects(data.selects);
        form.load(data.'.$name.');

        form.submit = async newData => {
            await Ajax.'.$name.'.update(newData);
            pageManager.goto(\'/'.$name.'\');
        }
    }
}
export class add {
    constructor(page, data) {
        let form = new FormManager(page.querySelector(\'form\'));
        if(data && data.selects)
            form.loadSelects(data.selects);

        form.submit = async newData => {
            await Ajax.'.$name.'.insert(newData);
            pageManager.goto(\'/'.$name.'\');
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
        $group->permission[1]->title = 'Edycja';
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

    function updateI18n($path, $name, $table)
    {
        $filename = $path.'/i18n.xml';
        $xml = simplexml_load_string(file_get_contents($filename));
        $group = $xml->addChild('node');
        $group->addAttribute('name', $name);
        $this->genereateI18nNode($group, $name);
        foreach ($table->column as $column) {
            $this->genereateI18nNode($group, $column->name);
        }
        $list = $xml->addChild('node');
        $list->addAttribute('name', $name.'List');

        $this->genereateI18nNode($list, 'header', 'List of elements of type '.$name, 'Lista elementów typu '.$name);

        file_put_contents($filename, $xml->asXML());
    }

    /**
     * @param \SimpleXMLElement $group
     * @param $column
     */
    private function genereateI18nNode(\SimpleXMLElement $group, $name, $valueEn = null, $valuePl = null): void
    {
        $node = $group->addChild('node');
        $node->addAttribute('name', $name);
        $en = $node->addChild('value');
        $en->addAttribute('lang', 'en');
        $en[0] = $valueEn ?? $name;
    }

    private function makeE2eTest($namespace, $name)
    {
        return '
        const BaseSeleniumTest = require("../../../E2eTests/Test/Selenium/baseSeleniumTest");
const {Key, By} = require("selenium-webdriver");
const {expect} = require("chai");
const {DataGenerator} = require("../../../E2eTests/Test/Selenium/DataGenerator");
const {E2eTestLog} = require("../../../E2eTests/Test/Selenium/E2eTestLog");

module.exports = class '.$name.'Test extends BaseSeleniumTest {
    constructor(driver) {
        super(driver);
        this.exampleItem = {
            "name": "sdfdsvrfd",
            "country": "pl",
            "VATIN": "8464865214",
            "REGON": "4356234554",
            "KRS": "98744536412",
            "address": "hgjyghfhbbdf",
            "postal": "00-000",
            "city": "sdfsdes"
        };
        this.exampleItemRandom = {
            "name": DataGenerator.generateRandomString(),
            "country": DataGenerator.generateRandomString(2),
            "VATIN": DataGenerator.generateRandomInt(1e9, 1e10 - 1).toString(),
            "REGON": DataGenerator.generateRandomInt(1e9, 1e10 - 1).toString(),
            "KRS": DataGenerator.generateRandomInt(1e9, 1e10 - 1).toString(),
            "address": DataGenerator.generateRandomString(),
            "postal": "00-000",
            "city": DataGenerator.generateRandomString()
        };
    }

    async mainTest() {
        await this.navigateToList();
        await this.addNew(this.exampleItem, true);
        await this.addNew(this.exampleItemRandom, false);
    }

    async navigateToList() {
        E2eTestLog.header("Navigate to list", 3)
        await this.clickElement("a[href=\"/'.$name.'\"]")
        await this.asleep(1000);
        await this.takeScreenshot("'.$name.'-list-before");
        for (const name in this.exampleItem) {
            const value = this.exampleItem[name];
            if (typeof value === "string" && value.length > 5) {
                expect(await this.driver.findElement(By.css(".page-'.$name.'-list")).getText()).to.not.contain(value);
            }
        }
    }

    async addNew(item, assert) {
        E2eTestLog.header(`Add new (assert: ${assert})`, 3)
        await this.clickElement("a[href="/'.$name.'/add"]")
        await this.asleep(1000);
        await this.takeScreenshot("'.$name.'-add-before", assert);
        for (const name in item) {
            const value = item[name];
            await this.sendKeysToElement("form [name=\"" + name + "\"]", value);
        }
        await this.asleep(200);
        await this.takeScreenshot("'.$name.'-add-filled", assert);
        await this.clickElement("form button");
        await this.asleep(2000);
        await this.takeScreenshot("'.$name.'-afterAdd", assert);
    }
};';
    }
}
