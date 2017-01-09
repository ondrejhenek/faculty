<?php
namespace Faculty;

/**
 * Interface between PHP and IS/STAG at Palacky University of Olomouc
 */
class Stag
{

	/**
	 * Gives a year number (2012, 2016,...) based on the actual day of the year
	 * Before 28th August gives calendar year-1
	 * @param string
	 * @return int
	 */
	public function getAcademicYear($date = null) {
		$timestamp = !empty($date) ? strtotime($date) : time();
		return date('Y', $timestamp - 20736000); // 60 * 60 * 24 = 240 days = 28th August
	}

	/**
	 * Gets semester of given date
	 * From 31. 1. to 3. 7. is LS then ZS
	 * @param string
	 * @return string LS/ZS
	 */
	public function getSemester($date = null) {
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



	/**
	 * Sends login info, checks ticket and responds with $user
	 * Could be redesigned to user only cookie ticket!!!
	 */
	public function sendLogin ($data, $debug = false)
	{
		$url = $this->loginUrl('http://www.entoo.cz');
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




	public function getIdentitiesByTicket($stagUserTicket) {
		$result = file_get_contents('https://stagservices.upol.cz/ws/services/rest/help/getStagUserListForLoginTicket?ticket='. $stagUserTicket .'&outputFormat=JSON');
		return json_decode($result, true)[0]['stagUserInfo'];
	}


	/**
	*
	*
	* returns null if failure
	**/
	public function getStudentInfoByOsobniCislo($osobniCislo, $academicYear, $ticket = null) {

		if (empty($academicYear)) {
			$academicYear = $this->getAcademicYear();
		}

		$options = [
			'http' => [
				'method' => 'GET'
			]
		];
		// sends ticket as cookie so server knows... ;)
		if (!empty($ticket)) {
			$options['http']['header'] = 'Cookie: WSCOOKIE='. $ticket;
		}
		$context = stream_context_create($options);
		$result = @file_get_contents('https://stagservices.upol.cz/ws/services/rest/student/getStudentInfo?osCislo='. $osobniCislo .'&outputFormat=JSON&rok='. $academicYear, false, $context);

		$student = json_decode($result, true)[0];
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
	public function getTeacherInfoByUcitidno ($ucitidno) {
		$result = @file_get_contents('https://stagservices.upol.cz/ws/services/rest/ucitel/getUcitelInfo?outputFormat=JSON&ucitIdno='. $ucitidno);
		return json_decode($result, true)[0];
	}


/** 
*  stdClass() {
*     
*  }
**/
	public function getCoursesForTeacher ($ucitidno, $semester) {
		$courses = @file_get_contents('https://stagservices.upol.cz/ws/services/rest/predmety/getPredmetyByUcitel?outputFormat=JSON&ucitIdno='. $ucitidno .'&semestr='. $semester);
		$result = json_decode($courses, true)[0];

		if (empty($result)) return null;
		return $result['predmetUcitele'];
	}


	/**
	 * Ziska ze STAGu seznam predmetu studenta podle zadaneho osobniho cisla a semestru (LS / ZS)
	 *
	 */
	public function getCoursesForStudent ($osobniCislo, $semester)
	{
		$courses = @file_get_contents('https://stagservices.upol.cz/ws/services/rest/predmety/getPredmetyByStudent?osCislo='. $osobniCislo .'&semestr='. $semester .'&outputFormat=JSON');

		$result = json_decode($courses, true)[0];

		if (empty($result)) return null;
		return $result['predmetStudenta'];
	}


	/**
	 * Ziska ze STAGu seznam studijnich programu podle fakulty
	 *
	 */
	public function getPrograms ($faculty = 'PFA')
	{
		$programs = @file_get_contents('https://stagservices.upol.cz/ws/services/rest/programy/getStudijniProgramy?kod=%25&fakulta='. $faculty .'&outputFormat=json');
		$result = json_decode($programs, true)[0];

		if (empty($result)) return null;
		return $result['programInfo'];
	}

	/**
	 * Ziska ze STAGu seznam studijnich oboru podle programu
	 *
	 */
	public function getFields ($programId)
	{
		$fields = @file_get_contents('https://stagservices.upol.cz/ws/services/rest/programy/getOboryStudijnihoProgramu?outputFormat=json&stprIdno='. $programId);
		$result = json_decode($fields, true)[0];

		if (empty($result)) return null;
		return $result['oborInfo'];
	}


	/**
	 * Processes Stag identities response and determines a role for user
	 *
	 * Teacher role for following:
	 * EP – ECTS koordinátor pracoviště
	 * FA – Tajemník fakulty
	 * FR – Fakultní rozvrhář
	 * KA – Katedra
	 * PR – Prorektor
	 * SR – Studijní referentka
	 * VY – Vyučující
	 * 
	 * Student role for following if they study at correct faculty (stagLoginLetter)
	 * ST – Student
	 * 
	 * Other roles and faculties are rejected
	 *
	 * @throws Exception - not a valid ticket
	 * @return array
	 */
	public function processIdentities($rawIdentities, $faculty = null) {

		$identities = [];
		foreach ($rawIdentities as $identity) {
			if (in_array($identity['role'], ['VY', 'EP', 'FA', 'FR', 'KA', 'PR', 'SR'])) {
				// skipping duplicates
				$existingKey = array_search($identity['ucitIdno'], $rawIdentities);
				if ($existingKey === false || $rawIdentities[$existingKey]['login'] != $identity['userName']) {
					$identities[] = [
						'login' => $identity['userName'],
						'ucitidno' => $identity['ucitIdno']
					];
				}
			} elseif ($identity['role'] == 'ST') {
				if (empty($faculty) || $identity['fakulta'] == $faculty) {
					$identities[] = [
						'osobniCislo' => $identity['userName'],
					];
				}
			}
		}

		// find duplicate identities in teacher

		if (empty($identities)) return false;
		return $identities;
	}




}