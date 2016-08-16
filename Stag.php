<?php
namespace Faculty;

use Exception;

/**
 * 
 *
 */
class Stag
{

	static public function getAcademicYear($date = null) {
		$timestamp = !empty($date) ? strtotime($date) : time();
		return date('Y', $timestamp - 20736000);
		// UPDATE items SET `year` =  DATE_SUB(`date`, INTERVAL 20736000 SECOND) WHERE year IS NULL
	}

	/**
	 * Gets semester of given date
	 * From 31. 1. to 3. 7. is LS then ZS
	 * @return string LS/ZS
	 */
	static public function getSemester($date = null) {
		$timestamp = !empty($date) ? strtotime($date) : time();
		$day = date('z', $timestamp) + 1;
		if ($day > 30 && $day < 210) {
			return 'LS';
		} else {
			return 'ZS';
		}
	}

	static public function loginUrl ($redirectUrl)
	{
		return 'https://stagservices.upol.cz/ws/login?originalURL='. $redirectUrl;
	}


/*
	public function verify($stagUserTicket, $user_id) {

		$ticketData = $this->getStagIdentityByTicket($stagUserTicket);
		$osobniCislo = $ticketData['id'];
		$role = $ticketData['role'];

		if (!$role == 'ST') {
			throw new Exception('Ups, STAG hlásí, že nejsi student. Napiš nám, něco s tím uděláme!');
		}

		// check duplicate accounts for verification at first.
		$duplicate = $this->find('first', array(
			'conditions' => array(
				'User.id !=' => $user_id,
				'User.upol_osobni_cislo' => $osobniCislo,
				'RolesUser.role_id' => AUTH_ROLE_STUDENT,
			),
			'fields' => array('User.email', 'RolesUser.id', 'RolesUser.role_id', 'RolesUser.user_id'),
			'joins' => array(
				array(
					'table' => 'roles_users',
					'alias' => 'RolesUser',
					'type' => 'INNER',
					'conditions' => array(
						'RolesUser.user_id = User.id',
					)
				)
			),
		));

		if ($duplicate) {
			throw new Exception('Osobní číslo <b>'. $osobniCislo .'</b> je již na Entoo registrováno pod účtem <b>'. $this->_obfuscate_email($duplicate['User']['email']) .'</b>. Přihlas se pomocí tohoto emailu, nebo nám napiš!');
		}

		$this->Role->addRole($user_id, AUTH_ROLE_STUDENT);
		$this->refreshRoleInSession($user_id);

		if (Auth::id() == $user_id) {
			$sessionUser = $this->Session->read('Auth.User');
			$this->Session->write('Auth.User', array_merge($sessionUser, $newData));
		}
		return true;

	}
*/


	/**
	 * Tests ticket validity
	 * 
	 * @throws Exception - not a valid ticket
	 * @return 
	 */
	static public function getStagIdentityByTicket($stagUserTicket, $stagLoginLetter) {
		$identities = Stag::getIdentityByTicket($stagUserTicket);
		if ($identities == null) {
			throw new Exception('Tvoje přihlášení do STAGu již vypršelo. Zkus to prosím znovu.');
		}

		$osobniCislo = false;
		$role = null;
		$ucitidno = null;
		foreach ($identities->stagUserInfo as $stagUser) {
			if ($stagUser->role == 'VY') {
				$role = 'VY';
				$osobniCislo = $stagUser->userName;
				$ucitidno = $stagUser->ucitIdno;
				break;
			}
			if ($stagUser->role == 'ST') {
				if (preg_match('/'. $stagLoginLetter .'\d*/', $stagUser->userName)) {
					$osobniCislo = $stagUser->userName;
					$role = 'ST';
					break;
				}
			}
		}

		return array('id' => $osobniCislo, 'role' => $role, 'ucitidno' => $ucitidno);
	}

	/**
	 * Fetches student data from Stag based on osobni cislo
	 *
	 */
	static public function getStudentInfo($osobniCislo) {
		$student = Stag::getStudentInfoByOsobniCislo($osobniCislo);

		$data = array(
			'upol_osobni_cislo' => $osobniCislo,
			'upol_portal_id' => $student->userName,
			'upol_programme' => $student->stprIdno,
			'name' => $student->jmeno,
			'surname' => mb_convert_case($student->prijmeni, MB_CASE_TITLE),
			'obor_id' => $student->oborIdnos, // POZOR! PRO VICE OBORU tu jsou 2 cisla oddelena carkou!
			'rocnik' => $student->rocnik,
			// 'upol_verified' => date("Y-m-d H:i:s"),
		);
		return $data;
	}

	/**
	 * Fetches teacher data from Stag based on ucitIdNo
	 *
	 */
	static public function getTeacherInfo($ucitidno) {
		$teacher = Stag::getTeacherInfoByUcitidno($ucitidno);
		
		$data = array(
			'name' => $teacher->jmeno,
			'surname' => $teacher->prijmeni,
			'fullname' => $teacher->titulPred .' '. $teacher->jmeno .' '. $teacher->prijmeni .' '. $teacher->titulZa,
			'email' => $teacher->email,
			'ucitidno' => $teacher->ucitIdno,
			// 'upol_verified' => date("Y-m-d H:i:s"),
		);
		return $data;
	}



/**
* getIdentityFromTicket
* Returns:
*	[  
*	    {  
*	        "stagUserInfo":[  
*	            {  
*	                "userName":"P11124",
*	                "role":"ST",
*	                "roleNazev":"Student",
*	                "fakulta":"PFA"
*	            },
*	            {  
*	                "userName":"F14572",
*	                "role":"ST",
*	                "roleNazev":"Student",
*	                "fakulta":"FIF"
*	            }
*	        ]
*	    }
*	]
*
*	[stagUserInfo] => Array
*	    (
*	        [0] => stdClass Object
*	            (
*	                [userName] => BUBELOVA
*	                [role] => VY
*	                [roleNazev] => Vyučující
*	                [fakulta] => PFA
*	                [katedra] => KTP
*	                [ucitIdno] => 1083
*	            )
*
*	        [1] => stdClass Object
*	            (
*	                [userName] => BUBELOVA
*	                [role] => VK
*	                [roleNazev] => Vedoucí pracoviště
*	                [katedra] => KTP
*	                [ucitIdno] => 1083
*	            )
*
*	    )
*/
	static public function getIdentityByTicket($stagUserTicket) {
		$result = file_get_contents('https://stagservices.upol.cz/ws/services/rest/help/getStagUserListForLoginTicket?ticket='. $stagUserTicket .'&outputFormat=JSON');
		return json_decode($result)[0];
	}

/** 
*
* object(stdClass) {
* 	osCislo => 'P11124'
* 	jmeno => 'John'
* 	prijmeni => 'GEALFOW'
* 	stav => 'S'
* 	userName => 'janija02'
* 	stprIdno => '567'
* 	nazevSp => 'Právo a právní věda'
* 	fakultaSp => 'PFA'
* 	kodSp => 'M6805'
* 	formaSp => 'P'
* 	typSp => 'M'
* 	czvSp => 'N'
* 	mistoVyuky => 'O'
* 	rocnik => '4'
* 	oborKomb => 'PRÁV'
* 	oborIdnos => '253'
* }
*
**/
	static public function getStudentInfoByOsobniCislo($osobniCislo) {
		$result = file_get_contents('https://stagservices.upol.cz/ws/services/rest/student/getStudentInfo?osCislo='. $osobniCislo .'&outputFormat=JSON&rok='. getAcademicYearFromDate());

		$student = json_decode($result)[0];

		if (!property_exists($student, 'userName')) {
			throw new Exception('Bohužel jsme tě ve STAGu nenašli. Napiš nám a zkontrolujeme to spolu.');
		}

		return $student;
	}


/** 
*  stdClass() {
*     'ucitIdno' => 1083,
*     'jmeno' => 'Kamila',
*     'prijmeni' => 'Bubelová',
*     'titulPred' => 'JUDr.',
*     'titulZa' => 'Ph.D.',
*     'platnost' => 'A',
*     'zamestnanec' => 'A',
*     'katedra' => 'KTP',
*     'pracovisteDalsi' => NULL,
*     'email' => 'bubelova@pf.upol.cz',
*     'telefon' => NULL,
*     'telefon2' => '0603/782957',
*     'url' => NULL,
*  }
**/
	static public function getTeacherInfoByUcitidno ($ucitidno) {
		$result = file_get_contents('https://stagservices.upol.cz/ws/services/rest/ucitel/getUcitelInfo?outputFormat=JSON&ucitIdno='. $ucitidno);
		if (!$result) throw new Exception("Bohužel se nám nepodařilo spojit se STAGem.");
		return json_decode($result)[0];
	}


/** 
*  stdClass() {
*     
*  }
**/
	static public function getCoursesForTeacher ($ucitidno, $semester) {
		$courses = file_get_contents('https://stagservices.upol.cz/ws/services/rest/predmety/getPredmetyByUcitel?outputFormat=JSON&ucitIdno='. $ucitidno .'&semestr='. $semester);

		if (!$courses) throw new Exception('Server UPOL neodpovídá. Zkus si synchronizovat předměty za chvíli.');

		return json_decode($courses, true)[0]['predmetUcitele'];
	}


	/**
	 * Ziska ze STAGu seznam predmetu studenta podle zadaneho osobniho cisla a semestru (LS / ZS)
	 *
	 */
	static public function getCoursesForStudent ($osobniCislo, $semester)
	{
		$courses = file_get_contents('https://stagservices.upol.cz/ws/services/rest/predmety/getPredmetyByStudent?osCislo='. $osobniCislo .'&semestr='. $semester .'&outputFormat=JSON');
		// $result = file_get_contents(TMP . DS . 'predmety_john.json');

		if (!$courses) throw new Exception('Server UPOL neodpovídá. Zkus si synchronizovat předměty za chvíli.');
		
		return json_decode($courses, true)[0]['predmetStudenta'];
	}


	/**
	 * Sends login info, checks ticket and responds with $user
	 */
	static public function sendLogin ($data, $debug = false)
	{
		$url = Stag::loginUrl('http://www.entoo.cz');
        $data = array('username' => $data['username'], 'password' => $data['password'], 'submit' => 'Přihlásit se', 'loginMethod' => 'jaas', 'originalURL' => 'http://www.entoo.cz');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);

        if ($debug) {
	        curl_setopt($curl, CURLOPT_VERBOSE, true);
	        $verbose = fopen('php://temp', 'w+');
	        curl_setopt($curl, CURLOPT_STDERR, $verbose);
	    }

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        
        if ($debug) {
	        debug($response);
	        rewind($verbose);
	        $verboseLog = stream_get_contents($verbose);
	        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";
	    }

	    // pokud tam neni redirect s ticketem, tak se to nepovedlo... FALSE!
        $pattern = '/stagUserTicket=(.*?)&/';
        if (!preg_match($pattern, $response, $ticket)) {
            return false;
        }

       	return $ticket[1];
	}


	/**
	 * Ziska ze STAGu seznam studijnich programu podle fakulty
	 *
	 */
	static public function getPrograms ($faculty = 'PFA')
	{
		$programs = file_get_contents('https://stagservices.upol.cz/ws/services/rest/programy/getStudijniProgramy?kod=%25&fakulta='. $faculty .'&outputFormat=json');

		if (!$programs) throw new Exception('Server UPOL neodpovídá. Možná je položen špatný dotaz.');
		
		return json_decode($programs, true)[0]['programInfo'];
	}

	/**
	 * Ziska ze STAGu seznam studijnich oboru podle programu
	 *
	 */
	static public function getFields ($programId)
	{
		$fields = file_get_contents('https://stagservices.upol.cz/ws/services/rest/programy/getOboryStudijnihoProgramu?outputFormat=json&stprIdno='. $programId);

		if (!$fields) throw new Exception('Server UPOL neodpovídá. Možná je položen špatný dotaz.');
		
		return json_decode($fields, true)[0]['oborInfo'];
	}


}