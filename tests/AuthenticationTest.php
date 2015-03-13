<?php
use ngfw\Authentication;

if (!isset($_SESSION)):
	$_SESSION = array();
endif;

class AuthenticationTest extends PHPUnit_Framework_TestCase
{
	public function testConstruction() {
		$a = new Authentication($dbAdapter = "database_Instance", $table = "tableName", $identityColumn = "IdentityColumn", $credentialColumn = "credentialColumn");
        $this->assertInstanceOf(get_class(new Authentication), $a);
    }

    public function testSetDBAdapter() {
        $a = new Authentication();
        $this->assertFalse($a->setDBAdapter(null));
        $this->assertInstanceOf(get_class(new Authentication), $a->setDBAdapter(new stdClass));
    }

    public function testSetDBTable() {
        $a = new Authentication();
        $this->assertFalse($a->setDBTable(null));
        $this->assertInstanceOf(get_class(new Authentication), $a->setDBTable("fakeTable"));
    }

    public function testSetIdentityColumn() {
        $a = new Authentication();
        $this->assertFalse($a->setIdentityColumn(null));
        $this->assertInstanceOf(get_class(new Authentication), $a->setIdentityColumn("fakeColumnName"));
    }

    public function testSetIdentity() {
        $a = new Authentication();
        $this->assertFalse($a->setIdentity(null));
        $this->assertInstanceOf(get_class(new Authentication), $a->setIdentity("fakeUsername"));
    }

    public function testSetCredentialColumn() {
        $a = new Authentication();
        $this->assertFalse($a->setCredentialColumn(null));
        $this->assertInstanceOf(get_class(new Authentication), $a->setCredentialColumn("fakeCredentialColumnName"));
    }

    public function testSetCredential() {
        $a = new Authentication();
        $this->assertFalse($a->setCredential(null));
        $this->assertInstanceOf(get_class(new Authentication), $a->setCredential("fakePassword"));
    }

    public function testAuth(){
    	$a = new Authentication();
    	$this->assertFalse($a->isValid());
    	$_SESSION['NG_AUTH'] = serialize(array("Username" => "Nick"));
    	$this->assertTrue($a->isValid());
    }

    public function testGetAuth(){
    	$a = new Authentication();
    	$this->assertFalse($a->getIdentity());
    	$_SESSION['NG_AUTH'] = serialize(array("Username" => "Nick"));
    	$this->assertEquals(array("Username" => "Nick"), $a->getIdentity());
    }

    public function testLogout(){
    	$a = new Authentication();
    	$_SESSION['NG_AUTH'] = serialize(array("Username" => "Nick"));
    	$a->clearIdentity();
    	$this->assertNull($_SESSION['NG_AUTH']);
    }

}