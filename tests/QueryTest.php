<?php
use ngfw\Query;
use ngfw\Exception;

class QueryTest extends PHPUnit_Framework_TestCase
{
    
    public function testQuery() {
        $q = new Query();
        $this->assertInstanceOf(get_class(new Query), $q->getQuery());
    }
    
    public function testQueryAsString() {
        $q = new Query();
        $this->assertEmpty($q->getQuery(true));
    }
    
    public function testSelect() {
        $q = new Query();
        $q->select()->from('TEST');
        $this->assertEquals('SELECT * FROM `TEST`', $q->getQuery(true));
    }
    
    public function testSelectField() {
        $q = new Query();
        $q->select("field1")->from('TEST');
        $this->assertEquals('SELECT `field1` FROM `TEST`', $q->getQuery(true));
    }
    
    public function testSelectFields() {
        $q = new Query();
        $q->select(array("field1", "field2"))->from('TEST');
        $this->assertEquals('SELECT `field1`, `field2` FROM `TEST`', $q->getQuery(true));
    }
    
    public function testSelectWhere() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\'', $q->getQuery(true));
    }
    
    public function testSelectWhereLimit() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->limit(1);
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' LIMIT 1', $q->getQuery(true));
    }
    
    public function testSelectWhereLimitWithOffset() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->limit("1,2");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' LIMIT 1,2', $q->getQuery(true));
    }
    
    public function testSelectWithDoubleWhere() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->andWhere('Username = ?', "gejadze");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' AND `Username` = \'gejadze\'', $q->getQuery(true));
    }
    
    public function testSelectWhereOrWhere() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->orWhere('Username = ?', "gejadze");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' OR `Username` = \'gejadze\'', $q->getQuery(true));
    }
    
    public function testSelectHaving() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->having('foo = ?', "bar");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' HAVING `foo` = \'bar\'', $q->getQuery(true));
    }
    
    public function testSelectGroup() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->group('foo');
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' GROUP BY `foo`', $q->getQuery(true));
    }
    
    public function testSelectGroupArray() {
        $q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->group(array('foo', 'bar'));
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' GROUP BY `foo`, `bar`', $q->getQuery(true));
    }


    public function testSelectOrder(){
    	$q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->order("NOW()");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' ORDER BY NOW()', $q->getQuery(true));
    }

    public function testSelectDoubleOrder(){
    	$q = new Query();
        $q->select()->from('TEST')->where('User = ?', "nick")->order("NOW()")->order("UserID", "ASC");
        $this->assertEquals('SELECT * FROM `TEST` WHERE `User` = \'nick\' ORDER BY NOW(), `UserID` ASC', $q->getQuery(true));
    }

    public function testJoin(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->join("TEST2", "TEST.superUserID = TEST2.userID");
        $this->assertEquals('SELECT * FROM `TEST` JOIN `TEST2` ON `TEST`.`superUserID` = `TEST2`.`userID`', $q->getQuery(true));
    }

    public function testJoinWithUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->join("TEST2", "UserID", "USING");
        $this->assertEquals('SELECT * FROM `TEST` JOIN `TEST2` USING (`UserID`)', $q->getQuery(true));
    }

    public function testJoinWithArrayUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->join("TEST2", array("UserID", "Username"), "using");
        $this->assertEquals('SELECT * FROM `TEST` JOIN `TEST2` USING (`UserID`, `Username`)', $q->getQuery(true));
    }

    public function testInnerJoin(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->innerJoin("TEST2", "TEST.superUserID = TEST2.userID");
        $this->assertEquals('SELECT * FROM `TEST` INNER JOIN `TEST2` ON `TEST`.`superUserID` = `TEST2`.`userID`', $q->getQuery(true));
    }

    public function testInnerJoinWithUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->innerJoin("TEST2", "UserID", "USING");
        $this->assertEquals('SELECT * FROM `TEST` INNER JOIN `TEST2` USING (`UserID`)', $q->getQuery(true));
    }

    public function testInnerJoinWithArrayUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->innerJoin("TEST2", array("UserID", "Username"), "using");
        $this->assertEquals('SELECT * FROM `TEST` INNER JOIN `TEST2` USING (`UserID`, `Username`)', $q->getQuery(true));
    }

    public function testLeftJoin(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->leftJoin("TEST2", "TEST.superUserID = TEST2.userID");
        $this->assertEquals('SELECT * FROM `TEST` LEFT JOIN `TEST2` ON `TEST`.`superUserID` = `TEST2`.`userID`', $q->getQuery(true));
    }

    public function testLeftJoinWithUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->leftJoin("TEST2", "UserID", "USING");
        $this->assertEquals('SELECT * FROM `TEST` LEFT JOIN `TEST2` USING (`UserID`)', $q->getQuery(true));
    }

    public function testLeftJoinWithArrayUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->leftJoin("TEST2", array("UserID", "Username"), "using");
        $this->assertEquals('SELECT * FROM `TEST` LEFT JOIN `TEST2` USING (`UserID`, `Username`)', $q->getQuery(true));
    }

    public function testRightJoin(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->rightJoin("TEST2", "TEST.superUserID = TEST2.userID");
        $this->assertEquals('SELECT * FROM `TEST` RIGHT JOIN `TEST2` ON `TEST`.`superUserID` = `TEST2`.`userID`', $q->getQuery(true));
    }

    public function testRightJoinWithUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->rightJoin("TEST2", "UserID", "USING");
        $this->assertEquals('SELECT * FROM `TEST` RIGHT JOIN `TEST2` USING (`UserID`)', $q->getQuery(true));
    }

    public function testRightJoinWithArrayUsing(){
    	$q = new ngfw\Query();
   		$q->select()
         ->from("TEST")
         ->rightJoin("TEST2", array("UserID", "Username"), "using");
        $this->assertEquals('SELECT * FROM `TEST` RIGHT JOIN `TEST2` USING (`UserID`, `Username`)', $q->getQuery(true));
    }

}
