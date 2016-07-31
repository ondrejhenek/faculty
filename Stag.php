<?php
namespace Faculty;

/**
 * 
 *
 */
class Stag
{

	protected function loginUrl ($redirectUrl)
	{
		return 'https://stagservices.upol.cz/ws/login?originalURL='. $redirectUrl;
	}

	public function facultyLogin()
	{
		
	}

	public function syncCourses()
	{

	}


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

	public function getStagIdentityByTicket($stagUserTicket) {
		// test ticket validity

		$identities = $this->getIdentityByTicket($stagUserTicket);
		if ($identities == null) {
			throw new Exception('Tvoje přihlášení do STAGu již vypršelo. Zkus to prosím znovu.');
		}

		// TOHLE ULOZ!!!! TADYHLE! DATA! VOLE!

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
				if (preg_match('/'. Configure::read('Faculty.stagLoginLetter') .'\d*/', $stagUser->userName)) {
					$osobniCislo = $stagUser->userName;
					$role = 'ST';
					break;
				}
			}
		}

		return array('id' => $osobniCislo, 'role' => $role, 'ucitidno' => $ucitidno);
	}

	public function getStudentInfoFromStag($osobniCislo) {
		$student = $this->getStudentInfoByOsobniCislo($osobniCislo);

		$data = array(
			'upol_osobni_cislo' => $osobniCislo,
			'upol_portal_id' => $student->userName,
			'upol_programme' => $student->stprIdno,
			'name' => $student->jmeno,
			'surname' => mb_convert_case($student->prijmeni, MB_CASE_TITLE),
			'obor_id' => $student->oborIdnos, // POZOR! PRO VICE OBORU tu jsou 2 cisla oddelena carkou!
			'rocnik' => $student->rocnik,
			'upol_verified' => date("Y-m-d H:i:s"),
		);
		return $data;
	}

	public function getTeacherInfoFromStag($ucitidno) {
		$teacher = $this->getTeacherInfoByUcitidno($ucitidno);
		
		$data = array(
			'name' => $teacher->jmeno,
			'surname' => $teacher->prijmeni,
			'fullname' => $teacher->titulPred .' '. $teacher->jmeno .' '. $teacher->prijmeni .' '. $teacher->titulZa,
			'email' => $teacher->email,
			'ucitidno' => $teacher->ucitIdno,
			'upol_verified' => date("Y-m-d H:i:s"),
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
			dbg($result);
			throw new Exception('STAGu? ...jsi tam? :( Vypadá to na chybu v komunikaci. Nahlas prosím tento problém ať to co nejdříve opravíme. Díky!');
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
	static public function getTeacherInfoByUcitidno($ucitidno) {
		$result = file_get_contents('https://stagservices.upol.cz/ws/services/rest/ucitel/getUcitelInfo?outputFormat=JSON&ucitIdno='. $ucitidno);
		return json_decode($result)[0];
	}


/** 
*  stdClass() {
*     
*  }
**/
	static public function getCoursesByUcitidno($ucitidno) {
		$result = file_get_contents('https://stagservices.upol.cz/ws/services/rest/predmety/getPredmetyByUcitel?outputFormat=JSON&ucitIdno='. $ucitidno);
		return json_decode($result)[0];
	}


}