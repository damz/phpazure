<?php
/**
 * Copyright (c) 2009, RealDolmen
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of RealDolmen nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY RealDolmen ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL RealDolmen BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Microsoft
 * @package    Microsoft_Azure
 * @subpackage UnitTests
 * @version    $Id: BlobStorageTest.php 14561 2009-05-07 08:05:12Z unknown $
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Microsoft_Azure_TableStorageTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Microsoft_Azure_Storage_Table */
require_once 'Microsoft/Azure/Storage/Table.php';

/**
 * @category   Microsoft
 * @package    Microsoft_Azure
 * @subpackage UnitTests
 * @version    $Id: BlobStorageTest.php 14561 2009-05-07 08:05:12Z unknown $
 * @copyright  Copyright (c) 2009, RealDolmen (http://www.realdolmen.com)
 * @license    http://phpazure.codeplex.com/license
 */
class Microsoft_Azure_TableStorageTest extends PHPUnit_Framework_TestCase
{
    static $path;
    
    public function __construct()
    {
        self::$path = dirname(__FILE__).'/_files/';
    }
    
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Microsoft_Azure_TableStorageTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Test setup
     */
    protected function setUp()
    {
    }
    
    /**
     * Test teardown
     */
    protected function tearDown()
    {
        $storageClient = $this->createStorageInstance();
        for ($i = 1; $i <= self::$uniqId; $i++)
        {
            try { $storageClient->deleteTable(TESTS_TABLE_TABLENAME_PREFIX . $i); } catch (Exception $e) { }
        }
    }
    
    protected function createStorageInstance()
    {
        $storageClient = null;
        if (TESTS_RUNONPROD)
        {
            $storageClient = new Microsoft_Azure_Storage_Table(TESTS_TABLE_HOST_PROD, TESTS_STORAGE_ACCOUNT_PROD, TESTS_STORAGE_KEY_PROD, false, Microsoft_Azure_RetryPolicy::retryN(10, 250));
            $storageClient->setCredentials(new Microsoft_Azure_SharedKeyLiteCredentials(TESTS_STORAGE_ACCOUNT_PROD, TESTS_STORAGE_KEY_PROD, false));
        }
        else
        {
            $storageClient = new Microsoft_Azure_Storage_Table(TESTS_TABLE_HOST_DEV, TESTS_STORAGE_ACCOUNT_DEV, TESTS_STORAGE_KEY_DEV, true, Microsoft_Azure_RetryPolicy::retryN(10, 250));
            $storageClient->setOdbcSettings(TESTS_TABLE_DEVCNSTRING, TESTS_TABLE_DEVCNUSER, TESTS_TABLE_DEVCNPASS);
        }

        return $storageClient;
    }
    
    protected static $uniqId = 0;
    
    protected function generateName()
    {
        self::$uniqId++;
        return TESTS_TABLE_TABLENAME_PREFIX . self::$uniqId;
    }
    
    /**
     * Test generate development table
     */
    public function testGenerateDevelopmentTable()
    {  
        if (!TESTS_RUNONPROD)
        {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->generateDevelopmentTable($tableName, 'TSTest_TestEntity');
            
            // NOTE 1: ODBC connection credentails should be specified.
            // NOTE 2: Table storage must be RESTARTED after generating tables. 
        }
    }
    
    /**
     * Test create table
     */
    public function testCreateTable()
    {
        if (TESTS_RUNONPROD) 
        {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $result = $storageClient->createTable($tableName);
            $this->assertEquals($tableName, $result->Name);
            
            $result = $storageClient->listTables();
            $this->assertEquals(1, count($result));
            $this->assertEquals($tableName, $result[0]->Name);
        }
    }
    
    /**
     * Test list tables
     */
    public function testListTables()
    {
        if (TESTS_RUNONPROD) 
        {
            $tableName1 = $this->generateName();
            $tableName2 = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $storageClient->createTable($tableName1);
            $storageClient->createTable($tableName2);

            $result = $storageClient->listTables();
            $this->assertEquals(2, count($result));
            $this->assertEquals($tableName1, $result[0]->Name);
            $this->assertEquals($tableName2, $result[1]->Name);
        }
    }
    
    /**
     * Test delete table
     */
    public function testDeleteTable()
    {
        if (TESTS_RUNONPROD) 
        {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            
            $storageClient->createTable($tableName);
            $storageClient->deleteTable($tableName);
            
            $result = $storageClient->listTables();
            $this->assertEquals(0, count($result));
        }
    }
    
    /**
     * Test insert entity
     */
    public function testInsertEntity()
    {
        if (TESTS_RUNONPROD) 
        {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entity = new TSTest_TestEntity('partition1', '000001');
            $entity->FullName = 'Maarten';
            $entity->Age = 25;
            $entity->Visible = true;
            
            $result = $storageClient->insertEntity($tableName, $entity);

            $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
            $this->assertEquals($entity, $result);
        }
    }
    
    /**
     * Test delete entity
     */
    public function testDeleteEntity()
    {
        if (TESTS_RUNONPROD) 
        {
            $tableName = $this->generateName();
            $storageClient = $this->createStorageInstance();
            $storageClient->createTable($tableName);
            
            $entity = new TSTest_TestEntity('partition1', '000001');
            $entity->FullName = 'Maarten';
            $entity->Age = 25;
            $entity->Visible = true;
            
            $result = $storageClient->insertEntity($tableName, $entity);

            $this->assertNotEquals('0001-01-01T00:00:00', $result->getTimestamp());
            $this->assertEquals($entity, $result);
            
            $storageClient->deleteEntity($tableName, $entity);
        }
    }
}

class TSTest_TestEntity extends Microsoft_Azure_Storage_TableEntity
{
    /**
     * @azure Name
     */
    public $FullName;
    
    /**
     * @azure Age Edm.Int64
     */
    public $Age;
    
    /**
     * @azure Visible Edm.Boolean
     */
    public $Visible = false;
}

// Call Microsoft_Azure_TableStorageTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Microsoft_Azure_TableStorageTest::main") {
    Microsoft_Azure_TableStorageTest::main();
}
