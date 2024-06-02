<?php
namespace ReleaseParser;

// Include pattern library
require_once __DIR__ . '/ReleasePatterns.php';

/**
 * ReleaseParser - A library for parsing scene release names.
 *
 * @package ReleaseParser
 * @author Wellington Estevo
 * @version 1.5.0
 */

class ReleaseParser extends ReleasePatterns
{
	/** @var string Original rls name. */
	private $release = '';
	/** @var mixed[] Release information vars. */
	public $data = [
		'release'		=> \null, // Original rls name
		'title'			=> \null, // First part of title
		'title_extra'	=> \null, // Second part of title (optional) like Name of track/book/xxx etc.
		'group'			=> \null,
		'year'			=> \null,
		'date'			=> \null,
		'season'		=> \null, // For TV rls
		'episode'		=> \null, // For TV/Audiobook/Ebook (issue) rls
		'disc'			=> \null, // For complete disc releases
		'flags'			=> \null, // Misc rls name flags
		'source'		=> \null,
		'format'		=> \null, // Rls format/encoding
		'resolution'	=> \null, // For Video rls
		'audio'			=> \null, // For Video rls
		'device'		=> \null, // For Software/Game rls
		'os'			=> \null, // For Software/Game rls
		'version'		=> \null, // For Software/Game rls
		'language'		=> \null, // Array with language code as key and name as value (in english)
		'country'		=> \null, // Release country
		'type'			=> \null,
	];


	/**
	 * ReleaseParser Class constructor.
	 * 
	 * The order of the parsing functions DO matter.
	 *
	 * @param string $release_name Original release name
	 * @param string $section Release section
	 * @return void 
	 */
	public function __construct( string $release_name, string $section = '' )
	{
		// Save orignal release name.
		$this->release = $release_name;
		$this->set( 'release', $this->release );

		// Parse everything.
		// The parsing order DO MATTER!
		$this->parseGroup();
		$this->parseFlags();			// Misc rls name flags
		$this->parseOs();				// For Software/Game rls: Operating System
		$this->parseDevice();			// For Software/Game rls: Device (like console)
		$this->parseVersion();			// For Software/Game rls: Version
		$this->parseEpisode();			// For TV/Audiobook/Ebook (issue) rls: Episode
		$this->parseSeason();			// For TV rls: Season
		$this->parseDate();
		$this->parseYear();
		$this->parseFormat();			// Rls format/encoding
		$this->parseSource();
		$this->parseResolution();		// For Video rls: Resolution (720, 1080p...)
		$this->parseAudio();			// For Video rls: Audio format
		$this->parseLanguage();		// Array with language code as key and name as value (in english)

		$this->parseSource();			// Source (2nd time, for right web source)
		$this->parseFlags();			// Flags (2nd time)
		$this->parseLanguage();			// Language (2nd time)

		$this->parseType( $section );
		$this->parseTitle();			// Title and extra title
		$this->parseCountry();			// Parses Release country
		$this->cleanupAttributes();	// Clean up unneeded and falsely parsed attributes
	}

	/**
	 * The __toString() method allows a class to decide how it will react when it is treated like a string.
	 * 
	 * https://www.php.net/manual/en/language.oop5.magic.php#object.tostring
	 *
	 * @return string $class_to_string Stringified attribute values.
	 */
	public function __toString(): string
	{
		$class_to_string = '';
		$type = \strtolower( $this->get( 'type' ) );

		// Loop all values and put together the stringified class
		foreach( $this->get( 'all' ) as $information => $information_value )
		{
			// Skip original release name and debug
			if ( $information === 'release' || $information === 'debug' ) continue;

			// Rename var title based on attributes
			if ( !empty( $this->get( 'title_extra' ) ) )
			{
				if ( $information === 'title' )
				{
					if ( $type === 'ebook' || $type === 'abook' )
					{
						$information = 'Author';
					}
					else if ( $type === 'music' || $type === 'musicvideo' )
					{
						$information = 'Artist';
					}
					else if ( $type === 'tv' || $type === 'anime' )
					{
						$information = 'Show';
					}
					else if ( $type === 'xxx' )
					{
						$information = 'Publisher';
					}
					else
					{
						$information = 'Name';
					}
				}
				// Rename title_extra based on attributes
				else if( $information === 'title_extra' )
				{
					if ( $this->hasAttribute( [ 'CD Single', 'Web Single', 'VLS' ], 'source' ) )
					{
						$information = 'Song';
					}
					else if ( $this->hasAttribute( [ 'CD Album', 'Vynil', 'LP' ], 'source' ) )
					{
						$information = 'Album';
					}
					else if ( $this->hasAttribute( [ 'EP', 'CD EP' ], 'source' ) )
					{
						$information = 'EP';
					}
					else
					{
						$information = 'Title';
					}
				}
			}
			else
			{
				// Sports without extra title
				if ( $type === 'sports' && $information === 'title' )
					$information = 'Name';
			}

			// Set ebook episode to Issue
			if ( $type === 'ebook' && $information === 'episode' )
				$information = 'Issue';

			// Value set?
			if ( isset( $information_value ) )
			{
				// Some attributes can have more then one value.
				// So put them together in this var.
				$values = '';

				// Date (DateTime) is the only obect,
				// So we have to handle it differently.
				if ( $information_value instanceof \DateTime )
				{
					$values = $information_value->format( 'd.m.Y' );
				}
				// Only loop of it's not a DateTime object
				else
				{
					$values = \is_array( $information_value ) ? $values . \implode( ', ', $information_value ) : $information_value;
				}

				// Separate every information type with a slash
				if ( !empty( $class_to_string ) )
					$class_to_string .= ' / ';

				$class_to_string .= \ucfirst( $information ) . ': ' . $values;
			}
		}

		return $class_to_string;
	}


	/**
	 * This method is called by var_dump().
	 * 
	 * https://www.php.net/manual/en/language.oop5.magic.php#object.debuginfo
	 *
	 * @return mixed $informations Removed vars without values.
	 */
	public function __debugInfo()
	{
		return $this->get( 'all' );
	}


	/**
	 * Parse release language/s.
	 *
	 * @return void
	 */
	private function parseLanguage()
	{
		$language_codes = [];

		// Search and replace pattern in regex pattern for better matching
		$regex_pattern = $this->cleanupPattern( $this->release, self::REGEX_LANGUAGE, [ 'audio', 'device', 'flags', 'format', 'group', 'os', 'resolution', 'source', 'year' ] );

		// Loop all languages
		foreach ( self::LANGUAGES as $language_code_key => $language_name )
		{
			// Turn every var into an array so we can loop it
			if ( !\is_array( $language_name ) )
				$language_name = [ $language_name ];

			// Loop all sub language names
			foreach ( $language_name as $name )
			{
				// Insert current lang pattern
				$regex = \str_replace( '%language_pattern%', $name, $regex_pattern );

				// Check for language tag (exclude "grand" for formula1 rls)
				\preg_match( $regex, $this->release, $matches );

				// Maybe regex error
				if ( preg_last_error() && \str_contains( $regex, '?<!' ) )
				{
					echo $regex . PHP_EOL;
				}

				if ( !empty( $matches ) )
					$language_codes[] = $language_code_key;
			}
		}

		if ( !empty( $language_codes ) )
		{
			$languages = [];

			foreach( $language_codes as $language_code )
			{
				// Get language name by language key
				$language = self::LANGUAGES[ $language_code ];
				// If it's an array, get the first value as language name
				if ( \is_array( $language ) )
					$language = self::LANGUAGES[ $language_code ][0];

				$languages[ $language_code ] = $language;
			}

			$this->set( 'language', $languages );
		}
	}


	/**
	 * Parse release date.
	 *
	 * @return void
	 */
	private function parseDate()
	{
		// Check for normal date
		\preg_match( '/[._\(-]' . self::REGEX_DATE . '[._\)-]/i', $this->release, $matches );

		$day = $month = $year = $temp = $date = '';

		if ( !empty( $matches ) )
		{
			// Date formats: 21.09.16 (default) / 16.09.2021 / 2021.09.16 / 09.16.2021
			$year = (int) $matches[1];
			$month = (int) $matches[2];
			$day = (int) $matches[3];

			// On older Mvid releases the format is year last.
			if ( \preg_match( self::REGEX_DATE_MUSIC, $this->release ) )
			{
				$temp = $year;
				$year = $day;
				$day = $temp;
			}

			// 4 digits day (= year) would change the vars.
			if ( \strlen( (string) $day ) == 4 )
			{
				$temp = $year;
				$year = $day;
				$day = $temp;
			}

			// Month > 12 means we swap day and month (16.09.2021)
			// What if day and month are <= 12?
			// Then it's not possible to get the right order, so date could be wrong.
			if ( $month > 12 )
			{
				$temp = $day;
				$day = $month;
				$month = $temp;
			}

			// 2 digits year has to be converted to 4 digits year
			// https://www.php.net/manual/en/datetime.createfromformat.php (y)
			if ( \strlen( (string) $year ) == 2 )
			{
				$year_new = 0;
				try
				{
					$year_new = \DateTime::createFromFormat( 'y', $year );
				}
				catch ( \Exception $e )
				{
					\trigger_error( 'Datetime Error (Year): ' . $year . ' / rls: ' . $this->release );
				}

				// If DateTime was created succesfully, just get the 4 digit year
				if ( !empty( $year_new ) )
					$year = $year_new->format( 'Y' );
			}

			// Build date string
			$date = $day . '.' . $month . '.' . $year;

			// Try to create datetime object
			// No error handling if it doesn't work.
			try
			{
				$this->set( 'date', \DateTime::createFromFormat( 'd.m.Y', $date ) );
			}
			catch ( \Exception $e )
			{
				\trigger_error( 'Datetime Error (Date): ' . $date . ' / rls: ' . $this->release );
			}
		}
		else
		{

			// Cleanup release name for better matching
			$release_name_cleaned = $this->cleanup( $this->release, 'episode' );

			// Put all months together
			$all_months = \implode( '|', self::MONTHS );
			// Set regex pattern
			$regex_pattern = \str_replace( '%monthname%', $all_months, self::REGEX_DATE_MONTHNAME );
			// Match day, month and year
			\preg_match_all( '/[._-]' . $regex_pattern . '[._-]/i', $release_name_cleaned, $matches );

			$last_result_key = $day = $month = $year = '';

			// If match: get last matched value (should be the right one)
			// Day is optional, year is a must have.
			if ( !empty( $matches[0] ) )
			{
				$last_result_key = array_key_last( $matches[0] );

				// Day, default to 1 if no day found
				$day = 1;
				if ( !empty( $matches[1][ $last_result_key ] ) )
				{
					$day = $matches[1][ $last_result_key ];
				}
				else if ( !empty( $matches[3][ $last_result_key ] ) )
				{
					$day = $matches[3][ $last_result_key ];
				}
				else if ( !empty( $matches[5][ $last_result_key ] ) )
				{
					$day = $matches[5][ $last_result_key ];
				}

				// Month
				$month = $matches[2][ $last_result_key ];

				// Year
				$year = $matches[4][ $last_result_key ];

				// Check for month name to get right month number
				foreach ( self::MONTHS as $month_number => $month_pattern )
				{
					\preg_match( '/' . $month_pattern . '/i', $month, $matches );

					if ( !empty( $matches ) )
					{
						$month = $month_number;
						break;
					}
				}

				// Build date string
				$date = $day . '.' . $month . '.' . $year;

				// Try to create datetime object
				// No error handling if it doesn't work.
				try
				{
					$this->set( 'date', \DateTime::createFromFormat( 'd.m.Y', $date ) );
				}
				catch ( \Exception $e )
				{
					\trigger_error( 'Datetime Error (Date): ' . $date . ' / rls: ' . $this->release );
				}
			}
		}
	}


	/**
	 * Parse release year.
	 *
	 * @return void
	 */
	private function parseYear()
	{
		// Remove any version so regex works better (remove unneeded digits)
		$release_name_cleaned = $this->cleanup( $this->release, 'version' );

		// Match year
		\preg_match_all( self::REGEX_YEAR, $release_name_cleaned, $matches );

		if ( !empty( $matches[1] ) )
		{
			// If we have any matches, take the last possible value (normally the real year).
			// Release name could have more than one 4 digit number that matches the regex.
			// The first number would belong to the title.
			// Sanitize year if it's not only numeric ("199X"/"200X")
			$year = \end( $matches[1] );
			$year = \is_numeric( $year ) ? (int) $year : $this->sanitize( $year );
			$this->set( 'year', $year );
		}
		// No Matches? Get year from parsed Date instead.
		else if ( !empty( $this->get( 'date' ) ) )
		{
			$this->set( 'year', $this->get( 'date' )->format( 'Y' ) );
		}
	}


	/**
	 * Parse release device.
	 *
	 * @return void
	 */
	private function parseDevice()
	{
		if ( !$this->isType( 'bookware' ) )
		{
			$device = $this->parseAttribute( self::DEVICE, 'device' );
			if ( !empty( $device ) )
			{
				// Only one device allowed, get last parsed occurence, may be the right one
				$device = \is_array( $device ) ? $device[ \count( $device ) - 1 ] : $device;
				$this->set( 'device', $device );
			}
		}
	}


	/**
	 * Parse release flags.
	 *
	 * @return void
	 */
	private function parseFlags()
	{
		$flags = $this->parseAttribute( self::FLAGS, 'flags' );

		if ( !empty( $flags ) )
		{
			// Always save flags as array
			$flags = !\is_array( $flags ) ? [ $flags ] : $flags;

			// Remove DC flag if DC device was parsed
			if (
				$this->get( 'device' ) === 'Sega Dreamcast' &&
				( $key = \array_search( 'Directors Cut', $flags ) ) !== \false
			)
			{
				unset( $flags[ $key ] );
			}

			$this->set( 'flags', $flags );
		}
	}


	/**
	 * Parse the release group.
	 *
	 * @return void
	 */
	private function parseGroup()
	{
		\preg_match( self::REGEX_GROUP, $this->release, $matches );

		if ( !empty( $matches[1] ) )
		{
			$this->set( 'group', $matches[1] );
		}
		else
		{
			$this->set( 'group', 'NOGRP' );
		}
	}

	/**
	 * Parse release version (software, games, etc.).
	 *
	 * @return void
	 */
	private function parseVersion()
	{
		if ( $this->isType( 'bookware' ) )
		{
			\preg_match( '/[._-]' . self::REGEX_VERSION_BOOKWARE . '[._-]/i', $this->get( 'release' ), $matches );
		}
		else if ( !$this->isType( 'ebook' ) && !$this->isType( 'abook' ) && !$this->isType( 'music' ) )
		{
			// Cleanup release name for better matching
			$release_name_cleaned = $this->cleanup( $this->release, [ 'flags', 'device' ] );
			$regex_pattern = '/[._-]' . self::REGEX_VERSION . '[._-]/i';
			\preg_match( $regex_pattern, $release_name_cleaned, $matches );
		}
		if ( !empty( $matches ) )
		{
			$version = \trim( $matches[1], '.' );
			// Remove win from version for some app cases
			if ( \str_contains( \strtolower( $version ), 'win' ) )
				$version = \trim( \str_replace( 'win', '', \strtolower( $version ) ), '.' );

			$this->set( 'version', $version );
		}
	}


	/**
	 * Parse release source.
	 *
	 * @return void
	 */
	private function parseSource()
	{
		if ( $this->isType( 'bookware' ) )
		{
			foreach( self::BOOKWARE as $bookware )
			{
				\preg_match( '/^' . $bookware . '[._-]/i', $this->release, $matches );

				if ( !empty( $matches ) )
				{
					$source = \trim( $matches[0], '.' );

					// If source is all uppercase = capitalize
					if ( $source === \strtoupper( $source ) )
					{
						$source = \ucwords( \strtolower( $source ) );
					}

					break;
				}
			}
		}
		else
		{
			$source = $this->parseAttribute( self::SOURCE, 'source' );
		}

		if ( !empty( $source ) )
		{
			// Only one source allowed, so get first parsed occurence (should be the right one)
			$source = \is_array( $source ) ? \reset( $source ) : $source;
			$this->set( 'source', $source );
		}
	}


	/**
	 * Parse release format/encoding.
	 *
	 * @return void
	 */
	private function parseFormat()
	{
		if ( !$this->isType( 'bookware' ) )
		{
			$format = $this->parseAttribute( self::FORMAT, 'format' );

			if ( !empty( $format ) )
			{
				// Only one format allowed, so get first parsed occurence (should be the right one)
				$format = \is_array( $format ) ? \reset( $format ) : $format;
				$this->set( 'format', $format );
			}
		}
	}


	/**
	 * Parse release resolution.
	 *
	 * @return void
	 */
	private function parseResolution()
	{
		if ( !$this->isType( 'bookware' ) && !$this->isType( 'ebook' ) && !$this->isType( 'abook' ) )
		{
			$resolution = $this->parseAttribute( self::RESOLUTION );

			if ( !empty( $resolution ) )
			{
				// Only one resolution allowed, so get first parsed occurence (should be the right one)
				$resolution = \is_array( $resolution ) ? \reset( $resolution ) : $resolution;
				$this->set( 'resolution', $resolution );
			}
		}
	}


	/**
	 * Parse release audio.
	 *
	 * @return void
	 */
	private function parseAudio()
	{
		if ( !$this->isType( 'bookware' ) && !$this->isType( 'ebook' ) )
		{
			$audio = $this->parseAttribute( self::AUDIO );
			if ( !empty( $audio ) ) $this->set( 'audio', $audio );
		}
	}


	/**
	 * Parse release operating system.
	 *
	 * @return void
	 */
	private function parseOs()
	{
		if ( !$this->isType( 'bookware' ) )
		{
			$os = $this->parseAttribute( self::OS );
			if ( !empty( $os ) ) $this->set( 'os', $os );
		}
	}


	/**
	 * Parse release season.
	 *
	 * @return void
	 */
	private function parseSeason()
	{
		if ( !$this->isType( 'bookware' ) && !$this->isType( 'ebook' ) )
		{
			\preg_match( self::REGEX_SEASON, $this->release, $matches );

			if ( !empty( $matches ) )
			{
				// key 1 = 1st pattern, key 2 = 2nd pattern
				$season = !empty( $matches[1] ) ? $matches[1] : \null;
				$season = empty( $season ) && !empty( $matches[2] ) ? $matches[2] : $season;

				if ( isset( $season ) )
				{
					$this->set( 'season', (int) $season );

					// We need to exclude some nokia devices, would be falsely parsed as season (S40 or S60)
					if (
						( $season == '40' || $season == '60' ) &&
						(
							$this->get( 'os' ) === 'Symbian' ||
							\preg_match( '/[._-]N(7650|66\d0|36\d0)[._-]/i', $this->release )
						)
					)
					{
						$this->set( 'season', \null );
					}
				}
			}
		}
	}


	/**
	 * Parse release episode.
	 *
	 * @return void
	 */
	private function parseEpisode()
	{
		// We need to exclude some nokia devices, would be falsely parsed as episode (beginning with N)
		if (
			$this->get( 'os' ) !== 'Symbian' &&
			$this->get( 'device' ) !== 'Playstation' &&
			!\preg_match( '/[._-]N(7650|66\d0|36\d0)[._-]/i', $this->release ) &&
			!$this->hasAttribute( [ 'Extended', 'Special Edition' ], 'flags' ) &&
			!$this->isType( 'bookware' )
		)
		{
			$regex_pattern = self::REGEX_EPISODE;
			if ( $this->isType( 'ebook' ) || $this->isType( 'abook' ) || $this->isType( 'music' ) )
			{
				$regex_pattern = self::REGEX_EPISODE_OTHER;
			}

			\preg_match( '/[._-]' . $regex_pattern . '[._-]/i', $this->release, $matches );

			if ( !empty( $matches ) )
			{
				// key 1 = 1st pattern, key 2 = 2nd pattern
				// 0 can be a valid value
				$episode = isset( $matches[1] ) && $matches[1] != '' ? $matches[1] : \null;
				$episode = !isset( $episode ) && isset( $matches[2] ) && $matches[2] != '' ? $matches[2] : $episode;

				if ( isset( $episode ) )
				{
					// Sanitize episode if it's not only numeric (eg. more then one episode found "1 - 2")
					if ( \is_numeric( $episode ) && $episode !== '0' )
					{
						$episode = (int) $episode;
					}
					else
					{
						$episode = $this->sanitize( \str_replace( [ '_', '.', 'a', 'A' ], '-', $episode ) );
					}
					$this->set( 'episode', $episode );
				}
			}
			else
			{
				\preg_match( '/[._-]' . self::REGEX_DISC . '[._-]/i', $this->release, $matches );

				if ( !empty( $matches ) )
				{
					$this->set( 'disc', (int) $matches[1] );
				}
			}
		}
	}

	
	/**
	 * Parses Release country and strips it from title.
	 * 
	 * @return void
	 */
	private function parseCountry()
	{
		if ( !\strtolower( $this->get( 'type' ) ) === 'tv' ) return;

		$title_words = \explode( ' ', $this->get( 'title' ) );
		$last_element = array_key_last( $title_words );
		$countries = '/^(US|UK|NZ|AU|CA|BE)$/i';
		$invalid_words_before = '/^(the|of|with|and|between|to)$/i';

		if ( $last_element === 0 ) return;

		if (
			preg_match( $countries, $title_words[ $last_element ] ) &&
			!preg_match( $invalid_words_before, $title_words[ $last_element - 1 ] )
		)
		{
			$this->set( 'country', $title_words[ $last_element ] );
			unset( $title_words[ $last_element ] );
			$this->set( 'title', join( ' ', $title_words ) );
		}
	}


	/**
	 * Parse Bookware type.
	 *
	 * @return bool
	 */
	private function isType( $type = '' )
	{
		if ( $type === 'sports' )
		{
			foreach( self::SPORTS as $sport )
				if ( \preg_match( '/^' . $sport . '[._]/i', $this->release ) )
					return \true;

			return \false;
		}
		else if ( $type === 'bookware' )
		{
			if ( $this->hasAttribute( 'Tutorial', 'flags' ) )
				return \true;

			if ( \preg_match( '/[._(-]bookware[._)-]/i', $this->release ) )
				return \true;

			foreach( self::BOOKWARE as $bookware )
				if ( \preg_match( '/^' . $bookware . '[._-]/i', $this->release ) )
					return \true;

			return \false;
		}
		else if ( $type === 'music' )
		{
			if ( \preg_match( '/^[\w()]+-+[\w().]+-+[\w()-]+$/i', $this->release ) )
				return \true;

			if ( \preg_match( '/^VA-/i', $this->release ) )
				return \true;

			return \false;
		}
		else if ( $type === 'ebook' )
		{
			if ( \preg_match( '/[._-](ebook(.\d+)?|comics?)-\w+$/i', $this->release ) )
				return \true;

			return \false;
		}
		else if ( $type === 'abook' )
		{
			if ( \preg_match( '/[._(-]A(?:UDiO?)?BOOKS?\d*[._)-]/i', $this->release ) )
				return \true;

			return \false;
		}
	}


	/**
	 * Parse the release type by section.
	 *
	 * @param string $section Original release section.
	 * @return void
	 */
	private function parseType( string &$section )
	{
		// 1st: guesss type by rls name
		$type = $this->guessTypeByParsedAttributes();
		// 2nd: no type found? guess by section
		$type = empty( $type ) ? $this->guessTypeBySection( $section ) : $type;
		// 3rd: set parsed type or default to Movie
		$type = empty( $type ) ? 'Movie' : $type;

		$this->set( 'type', $type );
	}


	/**
	 * Guess the release type by alerady parsed attributes.
	 *
	 * @return string $type Guessed type.
	 */
	private function guessTypeByParsedAttributes(): string
	{
		$type = '';

		// Match bookware
		if ( $this->isType( 'bookware' ) )
		{
			$type = $this->isType( 'ebook' ) ? 'eBook' : 'Bookware';
		}
		// Match sports events
		else if ( $this->isType( 'sports' ) )
		{
			$type = 'Sports';

			if (
				!empty( $this->get( 'device' ) ) ||
				$this->hasAttribute( self::FLAGS_GAMES, 'flags' ) ||
				$this->hasAttribute( self::SOURCES_GAMES, 'source' ) ||
				\in_array( $this->get( 'group' ), self::GROUPS_GAMES ) ||
				\in_array( $this->get( 'group' ), self::GROUPS_APPS )
			)
			{
				$type = 'Game';
			}
		}
		// Font = Font related flag
		else if ( $this->hasAttribute( [ 'FONT', 'FONTSET' ], 'flags' ) )
		{
			$type = 'Font';
		}
		// Abook = Abook related flag
		else if( $this->isType( 'abook' ) )
		{
			$type = 'ABook';
		}
		// Music related sources
		else if (
			$this->isType( 'music' ) ||
			$this->hasAttribute( self::SOURCES_MUSIC, 'source' ) ||
			$this->hasAttribute( self::FLAGS_MUSIC, 'flags' )
		)
		{
			$type = 'Music';

			if (
				!empty( $this->get( 'device' ) ) ||
				$this->hasAttribute( self::FLAGS_GAMES, 'flags' ) ||
				$this->hasAttribute( self::SOURCES_GAMES, 'source' ) ||
				\in_array( $this->get( 'group' ), self::GROUPS_GAMES )
			)
			{
				$type = 'Game';
			}
			else if ( \in_array( $this->get( 'group' ), self::GROUPS_APPS ) )
			{
				$type = 'App';
			}
			else if (
				!empty( $this->get( 'resolution') ) ||
				$this->hasAttribute( self::FORMATS_MVID, 'format' ) ||
				$this->hasAttribute( self::FORMATS_VIDEO, 'format' ) ||
				$this->hasAttribute( self::SOURCES_MVID, 'source' )
			)
			{
				$type = 'MusicVideo';
			}
			else if (
				( !empty( $this->get( 'version' ) ) && $this->get( 'source' ) === \null ) ||
				!empty( $this->get( 'os' ) ) ||
				$this->hasAttribute( self::FLAGS_APPS, 'flags' )
			)
			{
				$type = 'App';
			}
		}
		// Ebook = ebook related flag
		else if (
			$this->isType( 'ebook' ) ||
			$this->hasAttribute( self::FLAGS_EBOOK, 'flags' )
		)
		{
			$type = 'eBook';
		}
		// Anime related flags
		else if (
			$this->hasAttribute( self::FLAGS_ANIME, 'flags' ) ||
			$this->hasAttribute( 'RAWRip', 'source' )
		)
		{
			$type = 'Anime';
		}
		// Docu
		/*else if ( $this->hasAttribute( 'Docu', 'flags' ) )
		{
			$type = 'Docu';
		}*/
		// XXX
		else if ( $this->hasAttribute( self::FLAGS_XXX, 'flags' ) )
		{
			$type = 'XXX';
		}
		// Check for MVid formats
		else if (
			$this->hasAttribute( self::FORMATS_MVID, 'format' ) ||
			$this->hasAttribute( self::SOURCES_MVID, 'source' )
		)
		{
			$type = 'MusicVideo';
		}
		// Games = if device was found or game related flags
		else if (
			$this->hasAttribute( self::FLAGS_GAMES, 'flags' ) ||
			$this->hasAttribute( self::SOURCES_GAMES, 'source' ) ||
			\in_array( $this->get( 'group' ), self::GROUPS_GAMES )
		)
		{
			$type = 'Game';
		}
		// Games = if device was found or game related flags
		else if (
			!empty( $this->get( 'device' ) )
		)
		{
			$type = 'Game';

			if ( !empty( $this->get( 'os' ) ) )
				$type = 'App';
		}
		// Do We have an episode?
		else if (
			!empty( $this->get( 'episode' ) ) ||
			!empty( $this->get( 'season' ) ) ||
			$this->hasAttribute( self::SOURCES_TV, 'source' ) )
		{
			// Default to TV
			$type = 'TV';

			// Description with date inside brackets is nearly always music or musicvideo
			if ( \preg_match( self::REGEX_DATE_MUSIC, $this->get( 'release' ) ) )
			{
				$type = 'MusicVideo';
			}
			// Probably movie if not episode and season given
			else if (
				empty( $this->get( 'episode') ) &&
				empty( $this->get( 'season') )
			)
			{
				$type = 'Movie';
			}
		}
		// Description with date inside brackets is nearly always music or musicvideo
		else if ( \preg_match( self::REGEX_DATE_MUSIC, $this->get( 'release' ) ) )
		{
			$type = !empty( $this->get( 'resolution' ) ) ? 'MusicVideo' : 'Music';
		}
		// Has date and a resolution? probably TV
		else if (
			!empty( $this->get( 'date' ) ) &&
			!empty( $this->get( 'resolution' ) ) )
		{
			// Default to TV
			$type = 'TV';
		}
		// Not TV, so first check for movie related flags
		else if ( $this->hasAttribute( self::FLAGS_MOVIE, 'flags' ) )
		{
			$type = 'Movie';
		}
		// Music if music format and no version
		else if ( $this->hasAttribute( self::FORMATS_MUSIC, 'format' ) )
		{
			$type = 'Music';

			if ( !empty( $this->get( 'version' ) ) && $this->get( 'source' ) === \null )
				$type = 'App';
		}
		// App = if os is set or software (also game) related flags
		else if (
			(
				(
					!empty( $this->get( 'os' ) ) ||
					!empty( $this->get( 'version' ) ) ||
					$this->hasAttribute( self::FLAGS_APPS, 'flags' )
				) &&
				!$this->hasAttribute( self::FORMATS_VIDEO, 'format' )
			) ||
			\in_array( $this->get( 'group' ), self::GROUPS_APPS )
		)
		{
			$type = 'App';
		}
		// Last time to check for some movie stuff
		else if (
			$this->hasAttribute( self::SOURCES_MOVIES, 'source' ) ||
			$this->hasAttribute( self::FORMATS_VIDEO, 'format' ) ||
			( $this->get( 'resolution' ) &&
				(
					$this->get( 'year' ) ||
					$this->get( 'format' ) ||
					$this->get( 'source' ) === 'Bluray' ||
					$this->get( 'source' ) === 'DVD'
				)
			)
		)
		{
			$type = 'Movie';
		}

		return $type;
	}


	/**
	 * Guess the release type by section.
	 *
	 * @param string $section Original release section.
	 * @return string $type Guessed/Parsed release type.
	 */
	private function guessTypeBySection( string &$section ): string
	{
		$type = '';

		// No Section, no chocolate!
		if ( !empty( $section ) )
		{
			// Loop all types
			foreach ( self::TYPE as $type_parent_key => $type_value )
			{
				// Transform every var to array, so we can loop
				if ( !\is_array( $type_value ) )
					$type_value = [ $type_value ];

				// Loop all type patterns
				foreach ( $type_value as $value )
				{
					// Match type
					\preg_match( '/' . $value . '/i', $section, $matches );

					// Match found, set type parent key as type
					if ( !empty( $matches ) )
					{
						$type = $type_parent_key;
						break;
					}
				}

				if ( !empty( $type ) ) break;
			}
		}

		return $type;
	}


	/**
	 * Parse release title.
	 *
	 * @return void
	 */
	private function parseTitle()
	{
		$type = \strtolower( $this->get( 'type' ) );
		$release_name_cleaned = \str_replace( ',', '', $this->release );

		// Main title vars
		$title = $title_extra = \null;
		// Some vars for better debugging which regex pattern was used
		$regex_pattern = $regex_used = '';

		// We only break if we have some results.
		// If the case doenst't deliver results, it runs till default
		// which is the last escape and should deliver something.
		switch ( $type )
		{
			// Music artist + release title (album/single/track name, etc.)
			case 'music':
			case 'abook':
			case 'musicvideo':

				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_MUSIC;
				$regex_used = 'REGEX_TITLE_MUSIC';

				if ( $type === 'abook' )
				{
					$regex_pattern = self::REGEX_TITLE_ABOOK;
					$regex_used = 'REGEX_TITLE_ABOOK';
				}
				else if ( $type === 'musicvideo' )
				{
					$regex_pattern = self::REGEX_TITLE_MVID;
					$regex_used = 'REGEX_TITLE_MVID';
				}

				// Search and replace pattern in regex pattern for better matching
				$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'audio', 'flags', 'format', 'group', 'language', 'source' ] );

				// Special check for date:
				// If date is inside brackets with more words, it's part of the title.
				// If not, then we should consider and replace the regex date patterns inside the main regex pattern.
				if ( !\preg_match( self::REGEX_DATE_MUSIC, $release_name_cleaned ) )
				{
					$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'regex_date', 'regex_date_monthname', 'year' ] );
				}

				// Match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				if ( !empty( $matches ) )
				{
					// Full match
					$title = $matches[1];

					// Split the title in the respective parts
					$title_splitted = \explode( '-', $title );

					if ( !empty( $title_splitted ) )
					{
						// First value is the artist = title
						// We need the . for proper matching cleanup episode.
						$title = $this->cleanup( '.' . $title_splitted[0], 'episode' );

						// Unset this before the loop
						unset( $title_splitted[0] );

						// Separator
						$separator = $type === 'abook' ? ' - ' : '-';

						// Loop remaining parts and set title extra
						$i = 0;
						foreach( $title_splitted as $title_part )
						{
							// We need the . for proper matching cleanup episode.
							$title_part = $this->cleanup( '.' . $title_part . '.', 'episode' );
							$title_part = \trim( $title_part, '.' );

							if ( !empty( $title_part ) )
							{
								// First index is ok
								if (
									$i === 0 ||
									(
										// Other indexes ...
										$i > 0 &&
										(
											// ...only with certain chars
											\str_contains( $title_part, '_' ) ||
											\str_contains( $title_part, ')' ) ||
											\is_numeric( $title_part )
										)
									)
								)
								{
									$title_extra = !empty( $title_extra ) ? $title_extra . $separator . $title_part : $title_part;
								}
							}

							$i++;
						}
					}
					break;
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			// Software (Game + Apps)
			case 'game':
			case 'app':
				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_APP;
				$regex_used = 'REGEX_TITLE_APP';

				// Search and replace pattern release name
				//$release_name_cleaned = $this->cleanup( $release_name_cleaned, 'disc' );

				// Search and replace pattern in regex pattern for better matching
				$regex_pattern = $this->cleanupPattern( $release_name_cleaned, $regex_pattern, [ 'device', 'os', 'resolution' ] );

				// Also remove source if game
				if ( $type === 'game' )
				{
					$regex_pattern = $this->cleanupPattern( $release_name_cleaned, $regex_pattern, [ 'disc', 'language', 'source' ] );	
				}

				// Match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				if ( !empty( $matches ) )
				{
					$title = $matches[1];
					break;
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			// TV series
			case 'tv':
			case 'sports':
			case 'docu':

				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_TV;
				$regex_used = 'REGEX_TITLE_TV';

				// Only needed here for releases that have episodes
				// Maybe year is before episode and have to be removed
				$release_name_no_year = $this->cleanup( $release_name_cleaned, [ 'disc', 'format', 'year' ] );

				// Match title
				\preg_match( $regex_pattern, $release_name_no_year, $matches );

				// Check for matches with regex title tv
				if ( !empty( $matches ) )
				{
					$title = $matches[1];

					// Build pattern and try to get episode title
					// So search and replace needed data to match properly.
					$regex_pattern = self::REGEX_TITLE_TV_EPISODE;
					$regex_used .= ' + REGEX_TITLE_TV_EPISODE';

					// Search and replace pattern in regex pattern for better matching
					//$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'flags', 'format', 'language', 'resolution', 'source' ] );
					$release_name_cleaned = $this->cleanup( $release_name_cleaned, [ 'audio', 'flags', 'format', 'language', 'resolution', 'source' ] );

					// Match episode title
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );

					$title_extra = !empty( $matches[1] ) && $matches[1] !== '.' ? $matches[1] : '';

					// If multiple episodes (1-2) episodes get wrongfully parse as title, so remove it
					// The regex is 'too' ungreedy.
					if ( \is_numeric( $title_extra ) )
					{
						if (
							\strlen( $title_extra ) <= 2 &&
							\str_contains( $this->get( 'episode' ), (int) $title_extra )
						)
						{
							$title_extra = '';
						}
					}

					break;
				}
				// Try to match Sports match
				else
				{
					// Setup regex pattern
					$regex_pattern = self::REGEX_TITLE_TV_DATE;
					$regex_used = 'REGEX_TITLE_TV_DATE';

					// Search and replace pattern in regex pattern for better matching
					$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'flags', 'format', 'language', 'resolution', 'source', 'regex_date', 'year' ] );

					// Match Dated/Sports match title
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );

					if ( !empty( $matches ) )
					{
						// 1st match = event (nfl, mlb, etc.)
						$title = $matches[1];
						// 2nd match = specific event name (eg. team1 vs team2)
						$title_extra = !empty( $matches[2] ) && $matches[2] !== '.' ? $matches[2] : '';

						break;
					}
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			case 'anime':

				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_TV;
				$regex_used = 'REGEX_TITLE_TV';

				// Match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				// Check for matches with regex title tv
				if ( !empty( $matches ) )
				{
					$title = $matches[1];

					// Build pattern and try to get episode title
					// So search and replace needed data to match properly.
					$regex_pattern = self::REGEX_TITLE_TV_EPISODE;
					$regex_used .= ' + REGEX_TITLE_TV_EPISODE';

					// Search and replace pattern in regex pattern for better matching
					$release_name_cleaned = $this->cleanup( $release_name_cleaned, [ 'flags', 'format', 'language', 'resolution', 'source', 'year' ] );

					// Match episode title
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );

					$title_extra = !empty( $matches[1] ) ? $matches[1] : '';

					break;
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			// XXX
			case 'xxx':
				$matches = [];

				// Some XXX releases with episode
				if ( !empty( $this->get( 'episode' ) ) )
				{
					// Check for episode
					$regex_pattern = self::REGEX_TITLE_TV;
					$regex_used = 'REGEX_TITLE_TV';

					// Match title
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );
					
					// Check for matches with regex title tv
					if ( !empty( $matches ) )
					{
						$title = $matches[1];

						// Build pattern and try to get episode title
						// So search and replace needed data to match properly.
						$regex_pattern = self::REGEX_TITLE_TV_EPISODE;
						$regex_used .= ' + REGEX_TITLE_TV_EPISODE';

						// Search and replace pattern in regex pattern for better matching
						//$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'flags', 'format', 'language', 'resolution', 'source' ] );
						$release_name_cleaned = $this->cleanup( $release_name_cleaned, [ 'audio', 'flags', 'format', 'language', 'resolution', 'source' ] );

						// Match episode title
						\preg_match( $regex_pattern, $release_name_cleaned, $matches );

						$title_extra = !empty( $matches[1] ) && $matches[1] !== '.' ? $matches[1] : '';

						break;
					}
				}

				// Default pattern
				if ( empty( $title ) )
				{
					// Setup regex pattern
					$regex_pattern = !empty( $this->get( 'date' ) ) ? self::REGEX_TITLE_XXX_DATE : self::REGEX_TITLE_XXX;
					$regex_used = !empty( $this->get( 'date' ) ) ? 'REGEX_TITLE_XXX_DATE' : 'REGEX_TITLE_XXX';

					// Search and replace pattern in regex pattern for better matching
					$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'flags', 'year', 'language', 'source', 'regex_date', 'regex_date_monthname' ] );

					// Match title
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );

					if ( !empty( $matches ) )
					{
						// 1st Match = Publisher, Website, etc.
						$title = $matches[1];
						// 2nd Match = Specific release name (movie/episode/model name, etc.)
						$title_extra = !empty( $matches[2] ) ? $matches[2] : '';

						break;
					}
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			// Ebook
			case 'ebook':

				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_EBOOK;
				$regex_used = 'REGEX_TITLE_EBOOK';

				// Cleanup release name for better matching
				$release_name_cleaned = $this->cleanup( $release_name_cleaned, 'episode' );

				// Search and replace pattern in regex pattern for better matching
				$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'flags', 'format', 'language', 'regex_date', 'regex_date_monthname', 'year' ] );

				// Match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				if ( !empty( $matches ) )
				{
					// Full match
					$title = $matches[1];

					// Split the title in the respective parts
					$title_splitted = \explode( '-', $title );

					if ( !empty( $title_splitted ) )
					{
						// First value is the artist = title
						$title = $title_splitted[0];
						// Unset this before the loop
						unset( $title_splitted[0] );
						// Loop remaining parts and set title extra
						foreach( $title_splitted as $title_part )
						{
							if ( !empty( $title_part ) )
								$title_extra = !empty( $title_extra ) ? $title_extra . ' - ' . $title_part : $title_part;
						}
					}
					break;
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			// Font
			case 'font':

				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_FONT;
				$regex_used = 'REGEX_TITLE_FONT';

				// Cleanup release name for better matching
				$release_name_cleaned = $this->cleanup( $release_name_cleaned, [ 'version', 'os', 'format' ] );

				// Match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				if ( !empty( $matches ) )
				{
					$title = $matches[1];
					break;
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			case 'bookware':
				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_BOOKWARE;
				$regex_used = 'REGEX_TITLE_BOOKWARE';

				// Cleanup release name for better matching
				$release_name_cleaned = $this->cleanup( $release_name_cleaned, [ 'language', 'version' ] );

				// Match Dated/Sports match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				if ( !empty( $matches ) )
				{
					$title = $matches[1];
					break;
				}

				// Jump to default if no title found
				if ( empty( $title ) ) goto standard;

			// Movie
			default:

				// Jump in here for default matching
				standard:

				// Setup regex pattern
				$regex_pattern = self::REGEX_TITLE_TV_DATE;
				$regex_used = 'REGEX_TITLE_TV_DATE';

				// Search and replace pattern in regex pattern for better matching
				$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'flags', 'format', 'language', 'resolution', 'source' ] );

				// Cleanup release name for better matching
				if ( $type === 'xxx' )
				{
					$release_name_cleaned = $this->cleanup( $release_name_cleaned, [ 'episode', 'monthname', 'daymonth' ] );
				}

				// Try first date format
				// NFL.2021.01.01.Team1.vs.Team2.1080p...
				$regex_pattern = \str_replace( '%dateformat%', '(?:\d+[._-]){3}', $regex_pattern );

				// Match Dated/Sports match title
				\preg_match( $regex_pattern, $release_name_cleaned, $matches );

				if ( !empty( $matches ) && !empty( $matches[2] ) )
				{
					// 1st match = event (nfl, mlb, etc.)
					$title = $matches[1];
					// 2nd match = specific event name (eg. team1 vs team2)
					$title_extra = $matches[2];
				}
				else
				{
					// Setup regex pattern
					$regex_pattern = self::REGEX_TITLE_MOVIE;
					$regex_used = 'REGEX_TITLE_MOVIE';

					// Search and replace pattern in regex pattern for better matching
					$regex_pattern = $this->cleanupPattern( $this->release, $regex_pattern, [ 'audio', 'disc', 'flags', 'format', 'language', 'resolution', 'source', 'year' ] );

					// Match title
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );

					if ( !empty( $matches ) )
					{
						$title = $matches[1];
					}
					// No matches? Try simplest regex pattern.
					else
					{
						// Some very old (or very wrong named) releases dont have a group at the end.
						// But I still wanna match them, so we check for the '-'.
						$regex_pattern = self::REGEX_TITLE;
						$regex_used = 'REGEX_TITLE';

						// This should be default, because we found the '-'.
						if ( str_contains( $release_name_cleaned, '-' ) )
							$regex_pattern .= '-';

						// Match title
						\preg_match( '/^' . $regex_pattern . '/i', $release_name_cleaned, $matches );

						// If nothing matches here, this release must be da real shit!
						$title = !empty( $matches ) ? $matches[1] : '';
					}
				}
		}

		// Only for debugging
		$this->set( 'debug', $regex_used . ': ' . $regex_pattern );

		// Sanitize and set title
		$title = $this->sanitize( $title );
		$title = $title === 'VA' ? 'Various' : $title;
		$this->set( 'title', $title );

		// Sanitize and set title extra
		// Title extra needs to have null as value if empty string.
		$title_extra = empty( $title_extra ) ? \null : $title_extra;
		if ( isset( $title_extra ) )
			$this->set( 'title_extra', $this->sanitize( $title_extra ) );
	}


	/**
	 * Parse simple attribute.
	 *
	 * @param string $release_name Original release name.
	 * @param array $attribute Attribute to parse.
	 * @return mixed $attribute_keys Found attribute value (string or array).
	 */
	private function parseAttribute( array $attribute, string $type = '' )
	{
		$attribute_keys = [];
		$release_name_cleaned = $this->release;

		// Loop all attributes
		foreach ( $attribute as $attr_key => $attr_pattern)
		{
			// We need to catch the web source
			// NOT WORKING good if other stuff comes after web source, so removing it (29.07.2023)
			/*if ( $attr_key === 'WEB' )
			{
				$attr_pattern = $attr_pattern . '[._\)-](%year%|%format%|%language%|%group%|%audio%)';
				$attr_pattern = $this->cleanupPattern( $this->release, $attr_pattern, [ 'format', 'group', 'language', 'year', 'audio' ] );
			}*/

			// Transform all attribute values to array (simpler, so we just loop everything)
			if ( ! \is_array( $attr_pattern ) )
				$attr_pattern = [ $attr_pattern ];

			// Loop attribute values
			foreach ( $attr_pattern as $pattern )
			{
				// Regex flag = case Insensitive
				$flags = 'i';
				// The 'iT' source for iTunes needs to be case sensitive,
				// so italian language + it as word doesn't get parsed as source = itunes
				if ( $type === 'source' && $pattern === 'iT' )
				{
					$flags = '';
				}
				// Some special flags
				else if ( $type === 'flags' )
				{
					// Special flags that should only match if followed by specific information
					if (
						$attr_key === 'Final' ||
						$attr_key === 'New' ||
						$attr_key === 'V2' ||
						$attr_key === 'V3' ||
						$attr_key === 'Cover' ||
						$attr_key === 'Docu' ||
						$attr_key === 'HR' ||
						$attr_key === 'Vertical' ||
						$attr_key === 'TRAiNER'
					)
					{
						$pattern = $this->cleanupPattern( $release_name_cleaned, $pattern, [ 'flags', 'format', 'source', 'language', 'resolution' ] );
					}
				}

				// All separators
				$separators = '[._-]';
				$matches = \null;

				// Special cases: Some patterns have to match end of release name, so ignore firs regex results
				if ( \str_contains( $pattern, '$' ) )
				{
					$pattern = \str_replace( '$', '', $pattern );
					$regex_pattern = '/' . $separators . $pattern . '(?:-[\w.]+){1,2}$/' . $flags;

					// Check if pattern is inside release name
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );
				}
				else
				{
					// Default regex
					$regex_pattern = '/(' . $separators . ')' . $pattern . '\1/' . $flags;
					// Check if pattern is inside release name
					\preg_match( $regex_pattern, $release_name_cleaned, $matches );

					// Check if is last keyword before group
					if ( empty( $matches ) )
					{
						$regex_pattern = '/' . $separators . $pattern . '(?:-[\w.]+){1,2}$/' . $flags;
						\preg_match( $regex_pattern, $release_name_cleaned, $matches );
					}

					// Check with parenthesis
					if ( empty( $matches ) )
					{
						$regex_pattern = '/\(' . $pattern . '\)/' . $flags;
						\preg_match( $regex_pattern, $release_name_cleaned, $matches );
					}

					// No? Recheck with string at end of release name
					// - only format
					// - separator only ._
					// - if group is missing
					if ( empty( $matches ) && $type === 'format' )
					{
						$regex_pattern = '/[._]' . $pattern . '$/' . $flags;
						\preg_match( $regex_pattern, $release_name_cleaned, $matches );
					}
				}

				// Yes? Return attribute array key as value
				if ( !empty( $matches ) && !\in_array( $attr_key, $attribute_keys ) )
					$attribute_keys[] = $attr_key;
			}

			// Stop after first source found
			//if ( $type === 'source' && !empty( $attribute_keys ) )
				//break;
		}

		// Transform array to string if we have just one value
		if ( !empty( $attribute_keys ) )
		{
			if ( \count( $attribute_keys ) == 1 )
				$attribute_keys = \implode( $attribute_keys );

			return $attribute_keys;
		}
		return \null;
	}


	/**
	 * Check if rls has specified attribute value.
	 *
	 * @param mixed $values  Attribute values to check for (array or string)
	 * @param string $attribute_name  Name of attribute to look for (all array keys of the $data variable are possible) 
	 * @return boolean If attribute values were found
	 */
	public function hasAttribute( $values, $attribute_name )
	{
		// Get attribute value
		$attribute = $this->get( $attribute_name );

		// Check if attribute is set
		if ( isset( $attribute ) )
		{
			// Transform var into array for loop
			if ( !\is_array( $values ) )
				$values = [ $values ];

			// Loop all values to check for
			foreach ( $values as $value )
			{
				// If values were saved as array, check if in array
				if ( \is_array( $attribute ) )
				{
					foreach( $attribute as $attr_value )
					{
						if ( \strtolower( $value ) === \strtolower( $attr_value ) ) return \true;
					}
				}
				// If not, just check if the value is equal
				else
				{
					if ( \strtolower( $value ) === \strtolower( $attribute ) ) return \true;
				}
			}
		}

		return \false;
	}


	/**
	 * Cleanup release name from given attribute.
	 * Mostly needed for better title matching in some cases.
	 *
	 * @param string $release_name Original release name.
	 * @param mixed $information Informations to clean up (string or array).
	 * @return string $release_name_cleaned Cleaned up release name.
	 */
	private function cleanup( string $release_name, $informations ): string
	{
		// Just return if no information name was passed.
		if ( empty( $informations ) || empty( $release_name ) ) return $release_name;

		// Transform var into array for loop
		if ( !\is_array( $informations ) )
			$informations = [ $informations ];

		// Loop all attribute values to be cleaned up
		foreach ( $informations as $information )
		{
			// Get information value
			$information_value = $this->get( $information );
			// Get date as value if looking for "daymonth" or "month" (ebooks)
			if ( str_contains( $information, 'month' ) || str_contains( $information, 'date' ) )
				$information_value = $this->get( 'date' );

			// Only do something if it's not empty
			if ( isset( $information_value ) && $information_value != '' )
			{
				$attributes = [];

				// Get proper attr value
				switch ( $information )
				{
					case 'audio':
						// Check if we need to loop array
						if ( \is_array( $information_value ) )
						{
							foreach ( $information_value as $audio )
							{
								$attributes[] = self::AUDIO[ $audio ];
							}
						}
						else
						{
							$attributes[] = self::AUDIO[ $information_value ];
						}
						break;
					
					case 'daymonth':
						// Clean up day and month number from rls
						$attributes = [
							$information_value->format( 'd' ) . '(th|rd|nd|st)?',
							$information_value->format( 'j' ) . '(th|rd|nd|st)?',
							$information_value->format( 'm' )
						];
						break;
					
					case 'device':
						$attributes[] = self::DEVICE[ $information_value ];
						break;

					case 'disc':
						if ( !empty( $this->get( 'disc' ) ) )
							$attributes[] = self::REGEX_DISC;

						break;

					case 'format':
						// Check if we need to loop array
						if ( \is_array( $information_value ) )
						{
							foreach ( $information_value as $format )
							{
								$attributes[] = self::FORMAT[ $format ];
							}
						}
						else
						{
							$attributes[] = self::FORMAT[ $information_value ];
						}
						break;

					case 'episode':
						if ( $this->isType( 'ebook' ) || $this->isType( 'abook' ) )
						{
							$attributes[] = self::REGEX_EPISODE_OTHER;
						}
						else
						{
							$attributes[] = self::REGEX_EPISODE;
						}
						break;

					case 'flags':
						// Flags are always saved as array, so loop them.
						foreach ( $information_value as $flag )
						{
							// Skip some flags, needed for proper software/game title regex.
							if ( $flag != 'UPDATE' && $flag != '3D' )
								$attributes[] = self::FLAGS[ $flag ];
						}
						break;

					case 'language':
						foreach( $information_value as $language_code_key => $language )
						{
							$attributes[] = self::LANGUAGES[ $language_code_key ];
						}
						break;
						
					case 'monthname':
						// Replace all ( with (?: for non capturing
						$monthname = \preg_replace( '/\((?!\?)/i', '(?:', self::REGEX_DATE_MONTHNAME );
						// Get monthname pattern
						$monthname = \str_replace( '%monthname%', self::MONTHS[ $information_value->format( 'n' ) ], $monthname );
						$attributes[] = $monthname;
						break;

					case 'os':
						// Some old releases have "for x" before the OS
						if ( \is_array( $information_value ) )
						{
							foreach( $information_value as $value )
							{
								$attributes[] = self::OS[ $value ];
							}
						}
						else
						{
							$attributes[] = self::OS[ $information_value ];
						}
						break;

					case 'resolution':
						$attributes[] = self::RESOLUTION[ $information_value ];
						break;
					
					case 'source':
						// Needed for bookware source which is dynamic
						if ( \is_array( $information_value ) )
						{
							foreach( $information_value as $value )
							{
								if ( !empty( self::SOURCE[ $value ] ) )
								{
									$attributes[] = self::SOURCE[ $value ];
								}
							}
						}
						else if ( !empty( self::SOURCE[ $information_value ] ) )
						{
							$attributes[] = self::SOURCE[ $information_value ];
						}
						break;
						
					case 'version':
						if ( $this->isType( 'bookware' ) )
						{
							$attributes[] = self::REGEX_VERSION_BOOKWARE;
						}
						else
						{
							$attributes[] = self::REGEX_VERSION;
						}
						break;
					
					case 'year':
						$attributes[] = self::REGEX_YEAR_SIMPLE;
						break;

				}

				// Loop attributes if not empty and preg replace to cleanup
				if ( !empty( $attributes ) )
				{
					foreach ( $attributes as $attribute )
					{
						// Transform all values to array
						$attribute = !\is_array( $attribute ) ? [ $attribute ] : $attribute;
						foreach ( $attribute as $value )
						{
							// Exception for OS
							if ( $information === 'os' )
								$value = '(?:for[._-])?' . $value;

							$regex_pattern = '/[._(-]' . $value . '[._)-]/i';

							// Remove $ from specific patterns
							if ( \str_contains( $value, '$' ) )
							{
								$value = \str_replace( '$', '', $value );
								$regex_pattern = '/[._(-]' . $value . '-(?:[\w.-]+){1,2}$/i';
							}

							// We need to replace all findings with double dots for proper matching later on.
							$release_name = \preg_replace( $regex_pattern, '..', $release_name );

							// Replace format at the end if no group name
							if ( $information === 'format' )
								$release_name = \preg_replace( '/[._]' . $value . '$/i', '..', $release_name );
						}
					}
				}
			}
		}

		return $release_name;
	}


	/**
	 * Replace %attribute% in regex pattern with attribute pattern.
	 *
	 * @param string $release_name Original release name.
	 * @param string $regex_pattern The pattern to check.
	 * @param mixed $informations The information value to check for (string or array)
	 * @return string $regex_pattern Edited pattern
	 */
	private function cleanupPattern( string $release_name, string $regex_pattern, $informations ): string
	{
		// Just return if no information name was passed.
		if (
			empty( $informations ) ||
			empty( $release_name ) ||
			empty( $regex_pattern ) ) return $regex_pattern;

		// Transform to array
		if ( !\is_array( $informations ) )
			$informations = [ $informations ];

		// Loop all information that need a replacement
		foreach ( $informations as $information )
		{
			// Get information value
			$information_value = $this->get( $information );
			// Get date as value if looking for "daymonth" or "month" (ebooks,imgset, sports)
			if ( str_contains( $information, 'month' ) || str_contains( $information, 'date' ) )
				$information_value = $this->get( 'date' );

			// Only do something if it's not empty
			if ( isset( $information_value ) && $information_value != '' )
			{
				$attributes = [];

				switch( $information )
				{
					case 'audio':
						// Check if we need to loop array
						if ( \is_array( $information_value ) )
						{
							foreach ( $information_value as $audio )
							{
								$attributes[] = self::AUDIO[ $audio ];
							}
						}
						else
						{
							$attributes[] = self::AUDIO[ $information_value ];
						}
						break;

					case 'device':
						// Check if we need to loop array
						if ( \is_array( $information_value ) )
						{
							foreach ( $information_value as $device )
							{
								$attributes[] = self::DEVICE[ $device ];
							}
						}
						else
						{
							$attributes[] = self::DEVICE[ $information_value ];
						}
						break;

					case 'disc':
						$attributes[] = self::REGEX_DISC;
						break;

					case 'flags':
						// Flags are always saved as array, so loop them.
						foreach ( $information_value as $flag )
						{
							// Skip some flags, needed for proper software/game title regex.
							if ( $flag != '3D' )
								$attributes[] = self::FLAGS[ $flag ];
						}
						break;

					case 'format':
						// Check if we need to loop array
						if ( \is_array( $information_value ) )
						{
							foreach ( $information_value as $format )
							{
								$attributes[] = self::FORMAT[ $format ];
							}
						}
						else
						{
							$attributes[] = self::FORMAT[ $information_value ];
						}
						break;

					case 'group':
						$attributes[] = $information_value;
						break;

					case 'language':
						// Get first parsed language code
						$language_code = array_key_first( $information_value );
						$attributes[] = self::LANGUAGES[ $language_code ];
						break;

					case 'os':
						// Some old releases have "for x" before the OS
						if ( \is_array( $information_value ) )
						{
							foreach( $information_value as $value )
							{
								$attributes[] = self::OS[ $value ];
							}
						}
						else
						{
							$attributes[] = self::OS[ $information_value ];
						}
						break;

					case 'resolution':
						$attributes[] = self::RESOLUTION[ $information_value ];
						break;

					case 'regex_date':
						// Replace all ( with (?: for non capturing
						$attributes[] = \preg_replace( '/\((?!\?)/i', '(?:', self::REGEX_DATE );
						break;

					case 'regex_date_monthname':
						// Replace all ( with (?: for non capturing
						$regex_date_monthname = \preg_replace( '/\((?!\?)/i', '(?:', self::REGEX_DATE_MONTHNAME );
						// Get monthname pattern
						$regex_date_monthname = \str_replace( '%monthname%', self::MONTHS[ $information_value->format( 'n' ) ], $regex_date_monthname );
						$attributes[] = $regex_date_monthname;

						break;

					case 'source':
						// Needed for bookware source which is dynamic
						if ( \is_array( $information_value ) )
						{
							foreach( $information_value as $value )
							{
								if ( !empty( self::SOURCE[ $value ] ) )
								{
									$attributes[] = self::SOURCE[ $value ];
								}
							}
						}
						else if ( !empty( self::SOURCE[ $information_value ] ) )
						{
							$attributes[] = self::SOURCE[ $information_value ];
						}
						break;

					case 'year':
						$attributes[] = $information_value;
						break;

				}

				// Loop attributes if not empty and preg replace to cleanup
				if ( !empty( $attributes ) )
				{
					$values = '';

					foreach ( $attributes as $attribute )
					{
						$attribute = !\is_array( $attribute ) ? [ $attribute ] : $attribute;
						foreach ( $attribute as $value )
						{
							// Exception for OS
							if ( $information === 'os' )
								$value = '(?:for[._-])?' . $value;

							// Remove $ from specific patterns
							if ( \str_contains( $value, '$' ) )
							{
								$value = \str_replace( '$', '', $value );
								$value = $value . '-(?:[\w.-]+){1,2}$';
							}

							// Put to values and separate by | if needed.
							$values = !empty( $values ) ? $values . '|' . $value : $value;
						}
					}

					// Replace found values in regex pattern
					$regex_pattern = \str_replace( '%' . $information . '%', $values, $regex_pattern );
				}
			}
		}

		return $regex_pattern;
	}


	/**
	 * Remove unneeded attributes that were falsely parsed.
	 *
	 * @return void
	 */
	private function cleanupAttributes()
	{
		$type = \strtolower( $this->get( 'type' ) );

		if ( $type === 'movie' || $type === 'tv' )
		{
			if (
				!empty( $this->get( 'source' ) ) &&
				!empty( $this->get( 'format' ) ) &&
				!empty( $this->get( 'resolution' ) ) &&
				$this->get( 'source' ) == $this->get( 'format' )
			)
			{
				$this->set( 'format', \null );
			}
		}

		if ( $type === 'movie' )
		{			
			// Remove version if it's a movie (falsely parsed from release name)
			if ( $this->get( 'version' ) !== \null )
			{
				$this->set( 'version', \null );
			}
		}
		else if ( $type === 'app' )
		{
			// Remove audio if it's an App (falsely parsed from release name)
			if ( $this->get( 'audio' ) !== \null )
			{
				$this->set( 'audio', \null );
			}

			// Remove source if it's inside title
			if ( $this->get( 'source' ) !== null && \str_contains( $this->get( 'title' ), $this->get( 'source' ) ) )
			{
				$this->set( 'source', null );
			}
		}
		else if ( $type === 'game' )
		{
			// Remove Anime flag, not need for games
			$flags = $this->get( 'flags' );
			if (
				!empty( $flags ) &&
				( $key = array_search( 'Anime', $flags ) ) !== \false )
			{
				unset( $flags[ $key ] );
				$this->set( 'flags', $flags );
			}
		}
		else if ( $type === 'ebook' )
		{
			if ( $this->get( 'format' ) === 'Hybrid' )
			{
				// Remove Hybrid flag is format already Hybrid
				$flags = $this->get( 'flags' );
				if ( ( $key = array_search( 'Hybrid', $flags ) ) !== \false ) unset( $flags[ $key ] );
				$this->set( 'flags', $flags );
			}
		}
		else if ( $type === 'music' )
		{
			// Remove episode and season from music release, falsely parsed
			if ( !empty( $this->get( 'episode' ) ) ) $this->set( 'episode', \null );
			if ( !empty( $this->get( 'season' ) ) ) $this->set( 'season', \null );
		}
		// Remove flags from bookware, parsed stuff always part of title
		else if ( $type === 'bookware' )
		{
			$this->set( 'flags', \null );
		}

		if ( $type === 'movie' || $type === 'xxx' || $type === 'tv' )
		{
			// CHange source DVD to format DVDR if no res and format given
			if (
				$this->get( 'source' ) === 'DVD' &&
				empty( $this->get( 'resolution' ) ) &&
				empty( $this->get( 'format' ) )
			)
			{
				$this->set( 'format', 'DVDR' );
				$this->set( 'source', \null );
			}
		}

		if ( $type !== 'app' && $type !== 'game' && $this->hasAttribute( 'TRAiNER', 'flags' ) )
		{
			// Remove Trainer if not app or game
			$flags = $this->get( 'flags' );
			if ( ( $key = array_search( 'TRAiNER', $flags ) ) !== \false ) unset( $flags[ $key ] );
			$this->set( 'flags', $flags );
		}
	}


	/**
	 * Sanitize the title.
	 *
	 * @param string $title Parsed title.
	 * @return string $title Sanitized title.
	 */
	private function sanitize( string $text ): string
	{
		if ( !empty( $text ) )
		{
			// Trim '-' at the end of the string
			$text = \trim( $text, '-' );
			// Replace every separator char with whitespaces
			$text = \str_replace( [ '_', '.' ], ' ', $text );
			// Put extra whitespace between '-', looks better
			//$text = \str_replace( '-', ' - ', $text );
			// Trim and simplify multiple whitespaces
			$text = \trim( \preg_replace( '/\s{2,}/i', ' ', $text ) );

			// Check if all letters are uppercase:
			// First, check if we have more then 1 word in title (keep single worded titles uppercase).
			if ( \str_word_count( $text ) > 1 )
			{
				// Remove all whitespaces and dashes for uppercase check to work properly.
				$text_temp = \str_replace( [ '-', ' ' ], '', $text );
				if ( \ctype_upper( $text_temp ) )
				{
					// Transforms into lowercase, for ucwords to work properly.
					// Ucwords don't do anything if all chars are uppercase.
					$text = \ucwords( \strtolower( $text ) );
				}
			}

			$type = !empty( $this->get( 'type') ) ? $this->get( 'type') : '';
			
			// Words which should end with a point
			$special_words_after = [ 'feat', 'ft', 'incl', '(incl', 'inkl', 'nr', 'st', 'pt', 'vol' ];
			if ( \strtolower( $type ) != 'app' )
				$special_words_after[] = 'vs';
	
			// Words which should have a point before (usualy xxx domains)
			$special_words_before = [];
			if ( \strtolower( $type ) === 'xxx' )
				$special_words_before = [ 'com', 'net', 'pl' ];

			// Split title so we can loop
			$text_splitted = \explode( ' ', $text );

			// Loop, search and replace special words
			if ( \is_array( $text_splitted ) )
			{
				foreach( $text_splitted as $text_word )
				{
					// Point after word
					if ( \in_array( \strtolower( $text_word ), $special_words_after ) )
					{
						$text = \str_replace( $text_word, $text_word . '.', $text );
					}
					// Point before word
					else if ( \in_array( \strtolower( $text_word ), $special_words_before ) )
					{
						$text = \str_replace( ' ' . $text_word, '.' . $text_word , $text );
					}
				}
			}
		}

		return $text;
	}


	/**
	 * Get attribute value.
	 *
	 * @param string $name Attribute name.
	 * @return mixed Attribute value (array, string, int, date, null)
	 */
	public function get( string $name = 'all' )
	{
		// Check if var exists
		if ( isset( $this->data[ $name ] ) )
		{
			return $this->data[ $name ];
		}
		// Return all values
		else if ( $name === 'all' )
		{
			return $this->data;
		}

		return \null;
	}


	/**
	 * Set attribute value.
	 *
	 * @param string $name Attribute name to set.
	 * @param mixed $value Attribute value to set.
	 * @return true|false If value was succesfully set.
	 */
	private function set( string $name, $value )
	{
		// Check if array key alerady exists, so we don't create a new one
		if ( \array_key_exists( $name, $this->data ) )
		{
			$value = \is_array( $value ) && empty( $value ) ? \null : $value;
			$this->data[ $name ] = $value;
			return \true;
		}
		return \false;
	}
}