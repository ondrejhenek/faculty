<?php

// You can get it from getIndeititiesByTicket or at https://stagservices.upol.cz/ws/services/rest/users/getStagUserListForExternalLogin?outputFormat=json&externalLogin=LOGINXX
// like sidlja00
$fixture['getIdentitiesByTicket']['student'] =
[
	[
		'userName' => 'F14572',
		'role' => 'ST',
		'roleNazev' => 'Student',
		'fakulta' => 'FIF',
		'aktivni' => 'A',
	],
	[
		'userName' => 'P11124',
		'role' => 'ST',
		'roleNazev' => 'Student',
		'fakulta' => 'PFA',
		'aktivni' => 'A',
	]
];
$fixture['getIdentitiesByTicket']['doubleStudent'] =
[
	[
		'userName' => 'P14084',
		'role' => 'ST',
		'roleNazev' => 'Student',
		'fakulta' => 'PFA',
		'aktivni' => 'A',
	],
	[
		'userName' => 'P13223',
		'role' => 'ST',
		'roleNazev' => 'Student',
		'fakulta' => 'PFA',
		'aktivni' => 'A',
	]
];
$fixture['getIdentitiesByTicket']['studentTeacher'] =
[
	[
		'userName' => 'P14084',
		'role' => 'ST',
		'roleNazev' => 'Student',
		'fakulta' => 'PFA',
		'aktivni' => 'A',
	],
	[
		'userName' => 'BUBELOVA',
		'role' => 'VY',
		'roleNazev' => 'Vyučující',
		'katedra' => 'PFA',
		'katedra' => 'KTP',
		'ucitIdno' => '1083',
	]
];
$fixture['getIdentitiesByTicket']['teacher'] =
[
	[
		'userName' => 'BUBELOVA',
		'role' => 'VY',
		'roleNazev' => 'Vyučující',
		'fakulta' => 'PFA',
		'katedra' => 'KTP',
		'ucitIdno' => '1083',
	],
	[
		'userName' => 'BUBELOVA',
		'role' => 'VK',
		'roleNazev' => 'Vedoucí pracoviště',
		'katedra' => 'KTP',
		'ucitIdno' => '1083',
	]
];
$fixture['getIdentitiesByTicket']['alien'] =
[
	[
		'userName' => 'ALIEN',
		'role' => 'XX',
		'roleNazev' => 'Alien role',
	]
];
$fixture['getIdentitiesByTicket']['foreignStudent'] =
[
	[
		'userName' => 'F14572',
		'role' => 'ST',
		'roleNazev' => 'Student',
		'fakulta' => 'FIF',
		'aktivni' => 'A',
	],
];

$fixture['getIdentity']['teacher'] = [
	'id' => 'BUBELOVA',
	'role' => 'VY',
	'ucitidno' => '1083'
];
$fixture['getIdentity']['student'] = [
	'id' => 'janija02',
	'role' => 'ST',
	'ucitidno' => null
];

$fixture['getCoursesForStudent'] =
[
	[
	    'zkratka' => 'FI2',
	    'nazev' => 'Finance 2',
	    'katedra' => 'KAE',
	    'rok' => '2015',
	    'kredity' => 3,
	],
	[
	    'zkratka' => 'IKT',
	    'nazev' => 'Informační a komunikační technologie',
	    'katedra' => 'KAE',
	    'rok' => '2015',
	    'kredity' => 3,
	],
	[
	    'zkratka' => 'MG2',
	    'nazev' => 'Management 2',
	    'katedra' => 'KAE',
	    'rok' => '2015',
	    'kredity' => 3,
	]
];

$fixture['getCoursesForStudentExists'] =
[
	[
	    'zkratka' => 'NÚVOD',
	    'nazev' => 'Úvod do studia',
	    'katedra' => 'KPO',
	    'rok' => '2015',
	    'kredity' => 3,
	]
];

$fixture['getCoursesForTeacher'] =
[
	[
	    'zkratka' => 'NDS',
	    'nazev' => 'Diplomový seminář',
	    'katedra' => 'KTP',
	    'rok' => '2015',
	    'prednasejici' => 'NE',
	    'garant' => 'ANO',
	    'cvicici' => 'NE',
	    'seminarici' => 'NE',
	],
	[
	    'zkratka' => 'NEŘPT',
	    'nazev' => 'Exegeze římsko-právních textů',
	    'katedra' => 'KTP',
	    'rok' => '2015',
	    'prednasejici' => 'NE',
	    'garant' => 'ANO',
	    'cvicici' => 'NE',
	    'seminarici' => 'ANO',
	],
	[
	    'zkratka' => 'NŘPT',
	    'nazev' => 'Římské právo trestní',
	    'katedra' => 'KTP',
	    'rok' => '2015',
	    'prednasejici' => 'ANO',
	    'garant' => 'ANO',
	    'cvicici' => 'NE',
	    'seminarici' => 'ANO',
	]
];

$fixture['getFields'] =
[
	[
	    'oborIdno' => 253,
	    'nazev' => 'Právo',
	    'cisloOboru' => '6805T003',
	    'cisloSpecializace' => '00',
	    'typ' => 'Magisterský',
	    'forma' => 'Prezenční',
	    'jazyk' => 'CZ',
	    'fakulta' => 'PFA',
	    'platnyOd' => '2000',
	    'anotace' => 'Studenti studijního programu Právo a právní věda jsou během pěti let seznámeni se všemi právními odvětvími České republiky. Absolventi jsou připraveni na všechna právnická povolání, která mohou vykonávat po splnění zákonem stanovených podmínek. Jedná se zejména o práci advokátů, soudců, státních zástupců, notářů, vysokoškolských učitelů, podnikových právníků, právníků ve veřejné správě.',
	    'limitKreditu' => 300,
	    'pocetEtap' => 1,
	    'maxDelka' => 9,
	    'stdDelka' => 5,
	    'stprIdno' => 567,
	    'zkratka' => 'PRÁV',
	    'garant' => 'Halířová Gabriela, JUDr. Ph.D.',
	    'garantUcitIdno' => 1099,
	    'nazevProgramu' => 'Právo a právní věda',
	    'kodProgramu' => 'M6805',
	    'profilProgramu' => 'akademický',
	    'vzdelavaciCile' => 'Studenti studijního programu Právo a právní věda jsou během pěti let seznámeni se všemi právními odvětvími České republiky. Absolventi jsou připraveni na všechna právnická povolání, která mohou vykonávat po splnění zákonem stanovených podmínek. Jedná se zejména o práci advokátů, soudců, státních zástupců, notářů, vysokoškolských učitelů, podnikových právníků, právníků ve veřejné správě.',
	]
];

$fixture['getPrograms'] =
[
	[
	    'stprIdno' => 861,
	    'nazev' => 'Politologie',
	    'kod' => 'N6701',
	    'titul' => '7',
	    'titulZkr' => 'Mgr.',
	    'typ' => 'Navazující',
	    'forma' => 'Prezenční',
	    'fakulta' => 'PFA',
	    'stdDelka' => 2,
	    'maxDelka' => 5,
	    'kredity' => 120,
	    'celozivotni' => 'N',
	    'vykazovan' => 'A',
	    'platnyOd' => '2005',
	    'cile' => 'Hlavním cílem dvouletého magisterského programu je připravit odborníky, kteří propojí hluboké znalosti svého původního oboru (všechny typy univerzitního bakalářského nebo magisterského studia, včetně přírodovědných či lékařských oborů) s podrobnými znalostmi evropských integračních procesů a evropského práva. Program je rozdělen do tří základních bloků. V prvním bloku studenti získají znalosti základních předmětů politických a právních věd, nezbytných pro úspěšné studium evropských studií a evropského práva. Zbývající dva bloky jsou zaměřeny jednak na problematiku Evropské unie, jednak na evropské právo.',
	    'garant' => 'Fiala Vlastimil, doc. PhDr. CSc.',
	    'garantUcitIdno' => 661,
	    'jazyk' => 'Čeština',
	],
	[
	    'stprIdno' => 859,
	    'nazev' => 'Právní specializace',
	    'kod' => 'B6804',
	    'titul' => '0',
	    'titulZkr' => 'Bc.',
	    'typ' => 'Bakalářský',
	    'forma' => 'Prezenční',
	    'fakulta' => 'PFA',
	    'stdDelka' => 3,
	    'maxDelka' => 6,
	    'kredity' => 180,
	    'celozivotni' => 'N',
	    'vykazovan' => 'A',
	    'platnyOd' => '2005',
	    'cile' => 'Cílem oboru je poskytnutím základních klíčových, profesních i občanských kompetencí nezbytných pro výkon veřejné správy připravovat vysoce erudované odborníky pro výkon různých činností ve veřejné správě, pro které není vyžadováno magisterské právní vzdělání.',
	    'garant' => 'Sládeček Vladimír, prof. JUDr. DrSc.',
	    'garantUcitIdno' => 1338,
	    'jazyk' => 'Čeština',
	]
];
$fixture['getStudentInfoByOsobniCislo'] =
[
	'osCislo' => 'F14572',
	'jmeno' => 'John Altair',
	'prijmeni' => 'GEALFOW',
	'stav' => 'S',
	'userName' => 'janija02',
	'stprIdno' => '758',
	'nazevSp' => 'Humanitní studia',
	'fakultaSp' => 'FIF',
	'kodSp' => 'B6107',
	'formaSp' => 'P',
	'typSp' => 'B',
	'czvSp' => 'N',
	'mistoVyuky' => 'O',
	'rocnik' => '3',
	'oborKomb' => 'FI-AE',
	'oborIdnos' => '720,1180',
];

$fixture['getStudentInfoByOsobniCisloTicket'] =
[
	'osCislo' => 'F14572',
	'jmeno' => 'John Altair',
	'prijmeni' => 'GEALFOW',
	'stav' => 'S',
	'userName' => 'janija02',
	'stprIdno' => '758',
	'nazevSp' => 'Humanitní studia',
	'fakultaSp' => 'FIF',
	'kodSp' => 'B6107',
	'formaSp' => 'P',
	'typSp' => 'B',
	'czvSp' => 'N',
	'mistoVyuky' => 'O',
	'rocnik' => '3',
	'financovani' => '1',
	'oborKomb' => 'FI-AE',
	'oborIdnos' => '720,1180',
	'email' => 'janija02@ff.upol.cz',
	'cisloKarty' => '8A7C0E7A',
	'pohlavi' => 'M',
];

$fixture['getTeacherInfoByUcitidno'] =
[
	'ucitIdno' => 1083,
	'jmeno' => 'Kamila',
	'prijmeni' => 'Bubelová',
	'titulPred' => 'JUDr.',
	'titulZa' => 'Ph.D.',
	'platnost' => 'A',
	'zamestnanec' => 'A',
	'katedra' => 'KTP',
	'pracovisteDalsi' => NULL,
	'email' => 'bubelova@pf.upol.cz',
	'telefon' => NULL,
	'telefon2' => '0603/782957',
	'url' => NULL,
];

$fixture['sendLogin'] = '9d71b76b05acda8963b93cc9f85fddf6734a4941343a6d7567fc6cff14cfe7de';

return $fixture;