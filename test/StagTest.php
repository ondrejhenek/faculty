<?php
namespace Faculty\Test;

use PHPUnit\Framework\TestCase;
use Faculty\Stag;
use Exception;

// file_put_contents(dirname(__FILE__).'/asserts/'.__FUNCTION__, serialize($result));

class StagTest extends TestCase
{

	private $login = 'janija02';
	private $userName = 'F14572';
    // private $userName = 'P15274' // kdosi z pravniku
	private $password = '';
	private $role = 'ST'; // or VY
	private $year = '2016';
	private $financovani = 1;
	private $ucitidno = '1083';
	private $faculty = 'PFA';
	private $programId = '567';
	private $failing = 'XXXX';
	private $semester = 'LS';

	public function setUp()
	{
        $this->Stag = new Stag();
        $this->StagFixture = require __DIR__ . '/StagFixture.php';
	}

    public function testGetAcademicYear()
    {
        $result = $this->Stag->getAcademicYear('2016-08-29');
        $this->assertEquals(2016, $result);

        $result = $this->Stag->getAcademicYear('2016-08-27');
        $this->assertEquals(2015, $result);
    }

    public function testGetSemester()
    {
        $result = $this->Stag->getSemester('2016-01-30');
        $this->assertEquals('ZS', $result);

        $result = $this->Stag->getSemester('2016-02-01');
        $this->assertEquals('LS', $result);

        $result = $this->Stag->getSemester('2016-08-01');
        $this->assertEquals('ZS', $result);

        $result = $this->Stag->getSemester('2016-12-24');
        $this->assertEquals('ZS', $result);
    }

    public function testSendLoginFail()
    {
    	$ticket = $this->Stag->sendLogin(['username' => $this->failing, 'password' => $this->failing]);
    	$this->assertFalse($ticket);
    }

    public function testSendLoginSucc()
    {
        if (empty($this->password)) {
            throw new Exception('User password undefined! Tests depending on ticket won\'t work!');
        }

    	$ticket = $this->Stag->sendLogin(['username' => $this->login, 'password' => $this->password]);
    	$this->assertGreaterThan(20, strlen($ticket));
    	
    	return $ticket;
    }

	/**
     * @depends testSendLoginSucc
     */
    public function testGetIdentitiesByTicketStudent($ticket)
    {
    	$result = $this->Stag->getIdentitiesByTicket($ticket);
    	$this->assertEquals(1, count($result));
    	$this->assertArraySubset(['userName' => $this->userName, 'role' => $this->role], $result[0]);
    }

    public function testGetStudentInfoByOsobniCisloFail()
    {
    	$result = $this->Stag->getStudentInfoByOsobniCislo($this->failing, $this->year);
    	$this->assertEmpty($result);
    }


    public function testGetStudentInfoByOsobniCisloSuccess()
    {
    	$result = $this->Stag->getStudentInfoByOsobniCislo($this->userName, $this->year);
    	$this->assertArraySubset(['osCislo' => $this->userName], $result);
    }

	/**
     * @depends testSendLoginSucc
     */
    public function testGetStudentInfoByOsobniCisloTicket($ticket)
    {
    	$result = $this->Stag->getStudentInfoByOsobniCislo($this->userName, $this->year, $ticket);
    	$this->assertEquals($this->financovani, $result['financovani']);
    }

    public function testGetTeacherInfoByUcitidnoSucc()
    {
    	$result = $this->Stag->getTeacherInfoByUcitidno($this->ucitidno);
    	$this->assertArraySubset(['ucitIdno' => $this->ucitidno], $result);
    }


    public function testGetTeacherInfoByUcitidnoFail()
    {
    	$result = $this->Stag->getTeacherInfoByUcitidno($this->failing);
    	$this->assertEmpty($result);
    }


    public function testGetCoursesForTeacherSucc()
    {
    	$result = $this->Stag->getCoursesForTeacher($this->ucitidno, $this->semester);
    	$this->assertNotNull($result);
    	$this->assertArrayHasKey('zkratka', $result[0]);
    }

    public function testGetCoursesForTeacherFail()
    {
    	$result = $this->Stag->getCoursesForTeacher($this->failing, $this->failing);
    	$this->assertEmpty($result);
    }



    public function testGetCoursesForStudentSucc()
    {
    	$result = $this->Stag->getCoursesForStudent($this->userName, $this->Stag->getSemester());
    	$this->assertNotNull($result);
    	$this->assertArrayHasKey('zkratka', $result[0]);
    }

    public function testGetCoursesForStudentFail()
    {
    	$result = $this->Stag->getCoursesForStudent($this->failing, $this->semester);
    	$this->assertEmpty($result);
    }


    public function testGetProgramsSucc()
    {
    	$result = $this->Stag->getPrograms($this->faculty);
    	$this->assertNotNull($result);
    	$this->assertArrayHasKey('stprIdno', $result[0]);
    }

    public function testGetProgramsFail()
    {
    	$result = $this->Stag->getPrograms($this->failing);
    	$this->assertEmpty($result);
    }


    public function testGetFieldsSucc()
    {
    	$result = $this->Stag->getFields($this->programId);
    	$this->assertNotNull($result);
    	$this->assertArrayHasKey('stprIdno', $result[0]);
    }

    public function testGetFieldsFail()
    {
    	$result = $this->Stag->getFields($this->failing);
    	$this->assertEmpty($result);
    }

    public function testGetIdentityStudent()
    {
        $result = $this->Stag->getIdentity($this->StagFixture['getIdentitiesByTicket']['student'], 'P');

        $this->assertEquals([
            'id' => $this->StagFixture['getIdentitiesByTicket']['student'][1]['userName'],
            'role' => $this->StagFixture['getIdentitiesByTicket']['student'][1]['role'],
            'ucitidno' => false
        ], $result);
    }

    public function testGetIdentityTeacher()
    {
        $result = $this->Stag->getIdentity($this->StagFixture['getIdentitiesByTicket']['teacher'], 'P');

        $this->assertEquals([
            'id' => $this->StagFixture['getIdentitiesByTicket']['teacher'][0]['userName'],
            'role' => $this->StagFixture['getIdentitiesByTicket']['teacher'][0]['role'],
            'ucitidno' => $this->StagFixture['getIdentitiesByTicket']['teacher'][0]['ucitIdno']
        ], $result);
    }

    public function testGetIdentityAlien()
    {
        $result = $this->Stag->getIdentity($this->StagFixture['getIdentitiesByTicket']['alien'], 'P');

        $this->assertEquals(false, $result);
    }

    public function testGetIdentityForeignStudent()
    {
        $result = $this->Stag->getIdentity($this->StagFixture['getIdentitiesByTicket']['foreignStudent'], 'P');

        $this->assertEquals(false, $result);
    }



}
