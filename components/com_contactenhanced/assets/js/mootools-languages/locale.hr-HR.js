
Locale.define('hr-HR', 'Datume', {

	months: ['Sijećanj', 'Veljača', 'Ožujak', 'Travanj', 'Svibanj', 'Lipanj', 'Srpanj', 'Kolovoz', 'Rujan', 'Listopad', 'Studeni', 'Prosinac'],
	months_abbr: ['Sij', 'Velj', 'Ožu', 'Tra', 'Svi', 'Lip', 'Srp', 'Kol', 'Ruj', 'Lis', 'Stu', 'Pro'],
	days: ['Nedjelja', 'Ponedjeljak', 'Utorak', 'Srijeda', 'Četvrtak', 'Pteak', 'Subota'],
	days_abbr: ['Ned', 'Pon', 'Uto', 'Sri', 'Čet', 'Pet', 'Sub'],

	// Culture's date order: MM/DD/YYYY
	dateOrder: ['mjesec', 'dan', 'godina'],
	shortDate: '%m/%d/%Y',
	shortTime: '%I:%M%p',
	AM: 'AM',
	PM: 'PM',
	firstDayOfWeek: 0,

	// Date.Extras
	ordinal: function(dayOfMonth){
		// 1st, 2nd, 3rd, etc.
		return (dayOfMonth > 3 && dayOfMonth < 21) ? 'th' : ['th', 'st', 'nd', 'rd', 'th'][Math.min(dayOfMonth % 10, 4)];
	},

	lessThanMinuteAgo: 'Prije manje od minute',
	minuteAgo: 'prije minutu',
	minutesAgo: '{delta} minuta prije',
	hourAgo: 'Sat vremena ranije',
	hoursAgo: 'oko {delta} sati ranije',
	dayAgo: 'prije jednog dana',
	daysAgo: '{delta} dana ranije',
	weekAgo: 'prije 1 tjedna',
	weeksAgo: '{delta} tjedan ranije',
	monthAgo: 'prije jednog mjeseca',
	monthsAgo: '{delta}mjesec ranije',
	yearAgo: 'prije godinu dana',
	yearsAgo: '{delta} godina ranije',

	lessThanMinuteUntil: 'za manje od miute',
	minuteUntil: 'za manje od minute od sada',
	minutesUntil: '{delta} minuta od sada',
	hourUntil: 'odprilike za sat vremena od sada',
	hoursUntil: 'odprilike {delta} sati od sada',
	dayUntil: '1 dan od sada',
	daysUntil: '{delta} dana od sada',
	weekUntil: '1 tjedan od sada',
	weeksUntil: '{delta} tjedan od sada',
	monthUntil: '1 mjesec od sada',
	monthsUntil: '{delta} mjesec od sada',
	yearUntil: '1 godinu od sada',
	yearsUntil: '{delta} godinu od sada'

});


Locale.define('hr-HR', 'Broj', {

	decimal: '.',
	group: ',',

/* 	Commented properties are the defaults for Number.format
	decimals: 0,
	precision: 0,
	scientific: null,

	prefix: null,
	suffic: null,

	// Negative/Currency/percentage will mixin Number
	negative: {
		prefix: '-'
	},*/

	currency: {
//		decimals: 2,
		prefix: '$ '
	}/*,

	percentage: {
		decimals: 2,
		suffix: '%'
	}*/

});


Locale.define('hr-HR', 'FormValidator', {

	required: 'Ovo polje je obavezno.',
	minLength: 'Molimo unesite barem {minLength} jedan znak (unjeli ste {length} znakova).',
	maxLength: 'Molimo unesite NE više od {maxLength} znakova (unjeli ste {length} znakova).',
	integer: 'Molimo unesite broj u ovo polje. Brojevi sa decimalama nisu dozvoljeni (npr. 1.25) .',
	numeric: 'Molimo unesite samo brojčane vrijednosti u polje (npr. "1" ili "1.1" ili "-1" ili "-1.1").',
	digits: 'Molimo unesite samo brojeve i punktacije u ovo polje (npr., broj telefona sa crticama i točkama su dozvoljeni).',
	alpha: 'Molimo unesite samo slova (a-z) u ovo polje. Nisu dozvoljeni razmaci i drugi znakovi.',
	alphanum: 'Molimo unesite samo slova (a-z) ili brojeve (0-9) u ovo polje. Razmaci i drugi znakovi su dozvoljeni.',
	dateSuchAs: 'Molimo unesite ispravan datum kao {date}',
	dateInFormatMDY: 'Molimo unesite ispravan datum kao MM/DD/YYYY (npr. "12/31/1999")',
	email: 'Molimo unesite ispravnu email adresu. Npr "fred@domain.com".',
	url: 'Molimo unesite ispravan URL kao npr. http://www.example.com.',
	currencyDollar: 'Molimo unesite ispravan $ iznos. Npr. Kn100.00 .',
	oneRequired: 'Molimo unesite barem jednu vrijednost.',
	errorPrefix: 'Greška: ',
	warningPrefix: 'Upozorenje: ',

	// Form.Validator.Extras
	noSpace: 'Ne možete ostaviti prazno mjesto - razmak - u ovom polju.',
	reqChkByNode: 'Nije odabrana stavka.',
	requiredChk: 'Ovo polje je obavezno.',
	reqChkByName: 'Molimo odaberite {label}.',
	match: 'Ovo polje se mora poklapati sa poljem {matchName}',
	startDate: 'datum početka',
	endDate: 'datum završetka',
	currendDate: 'trenutni datum',
	afterDate: 'Datum mora biti jednak ili kasniji {label}.',
	beforeDate: 'Datum mora biti jednak ili raniji {label}.',
	startMonth: 'Molimo odaberite mjesec početka',
	sameMonth: 'Ova dva datuma moraju biti u istom mjesecu - Jedan morate ispraviti.',
	creditcard: 'Broj kreditne kartice je neispravan. Molimo provjerite unos i pokušajte ponovno. {length} uneseni brojevi.'

});