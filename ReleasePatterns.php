<?php
namespace ReleaseParser;

/**
 * ReleasePatterns - All needed patterns for properly parsing releases.
 *
 * @package ReleaseParser
 * @author Wellington Estevo
 * @version 1.4.4
 */

class ReleasePatterns {

	// Declaration of some needed regex patterns.
	// https://regex101.com/ is your best friend for testing those patterns.
	// %varname% will be replaced with the parsed valued for better macthing.

	// Find language (old: (?!sub))
	const REGEX_LANGUAGE = '/[._(-]%language_pattern%[._)-][._(-]?(?:%source%|%format%|%audio%|%flags%|%year%|%os%|%device%|%resolution%|bookware|[ae]book|(?:us|gbr|eng|nl|fi|fr|no|dk|de|se|ice)|multi|ml[._)-]|dl[._)-]|dual[._-]|%group%)/i';
	// Find date
	const REGEX_DATE = '(\d{2}|\d{4})[._-](\d{2})[._-](\d{2}|\d{4})';
	// Special date with month name: 24th January 2002 / Sep. 2000 day 5 / January 2000 1
	const REGEX_DATE_MONTHNAME = '(\d{1,2})?(?:th|rd|st|nd)?[._-]?(%monthname%)[._-]?(\d{1,2})?(?:th|rd|st|nd)?[._-]?(\d{4})[._-]?(?:day[._-]?)?(\d{1,2})?';
	// Description with date inside brackets is nearly always music or musicvideo
	const REGEX_DATE_MUSIC = '/\([a-z._]+[._-]' . self::REGEX_DATE . '\)/i';
	// Get right year
	const REGEX_YEAR_SIMPLE = '(19\d[\dx]|20\d[\dx])';
	const REGEX_YEAR = '/(?=[(._-]' . self::REGEX_YEAR_SIMPLE . '[)._-])/i';
	// Extract group
	const REGEX_GROUP = '/-([\w.]+)$/i';
	// Extract OS
	//const REGEX_OS = '';
	// Episode pattern matches: S01E01 / 1x01 / E(PS)1 / OVA1 / F123 / Folge_123 / Episode 1 / Issue 1 etc.
	// Good for tv and audiobook rls
	const REGEX_EPISODE = '(?:(?:s\d+[._-]?)?(?:ep?|eps[._-]?|episode[._-]?|o[av]+[._-]?|f(?:olge[._-]?)?)([\d_-]+)|(?:\d+x)(\d+))';
	const REGEX_EPISODE_OTHER = '(?:ep?|eps[._-]?|se[._-]?|episode[._-]?|f(?:olge[._-]?)?|band[._-]?|issue[._-]?|ausgabe[._-]?|n[or]?[._-]?|(?:silber[._-])?edition[._-]?|sets?[._-]?)([\d_-]+)';
	const REGEX_EPISODE_TV = '(?<!^)(?:(?:(?:[ST]|saison|staffel|temp)[._-]?\d+)?[._-]?(?:ep?|o[av]+[._-]?|eps[._-]?|episode[._-]?)[\d-]+|\d+x\d+|[ST]\d+)';
	// For Disc numbers: Disc1 / DVD1 / CD1 / (S01)D01
	const REGEX_DISC = '(?:s\d+[._-]?)?(?:d|di[cks][cks]|cd|dvd)[._-]?(\d+)';
	// Season pattern matches: S01E01 / 1x01 / S01D01
	const REGEX_SEASON = '/[._-](?:(?:[ST]|saison|staffel|temp)[._-]?(\d+)[._-]?(?:(?:ep?|eps[._-]?|episode[._-]?|f(?:olge[._-]?)|d|di[cks][cks][._-]?|cd[._-]?|dvd[._-]?)\d+)?|(\d+)(?:x\d+))[._-]/i';
	// Basic title pattern
	const REGEX_TITLE = '([\w\\&.()-]+)';
	// Good for Ebooks
	const REGEX_TITLE_EBOOK = '/^' . self::REGEX_TITLE . '[._(-]+(?:%year%|%language%|%flags%|%format%|%regex_date%|%regex_date_monthname%|ebook)[._)-]/iU';
	// Good for Fonts
	const REGEX_TITLE_FONT = '/^' . self::REGEX_TITLE . '-/i';
	// Good for Movies
	const REGEX_TITLE_MOVIE = '/^' . self::REGEX_TITLE . '[._(-]+(?:%disc%|%year%|%language%|%source%|%flags%|%format%|%resolution%|%audio%)[._)-]/iU';
	const REGEX_TITLE_MOVIE_EXTRA = '/%year%[._-]' . self::REGEX_TITLE . '[._(-]\.+/iU'; // ungreedy
	// Music pattern matches: Author_2.0_(Name)-Track_album_title_2.0_-_track_bla_(Extended_edition)-...
	// Good for music releases and Audiobooks
	const REGEX_TITLE_MUSIC = '/^' . self::REGEX_TITLE . '(?:\([\w-]+\))?[._(-]+(?:\(?%source%\)?|%year%|%group%|%audio%|%flags%|%format%|%regex_date%|%regex_date_monthname%|%language%[._)-])/iU';
	const REGEX_TITLE_ABOOK = '/^' . self::REGEX_TITLE . '[._(-]+(?:%source%[._)-]?|A(?:UDiO?)?BOOKs?|%year%|%group%|%audio%|%flags%|%format%|%language%[._)-])/iU';
	const REGEX_TITLE_MVID = '/^' . self::REGEX_TITLE . '[._(-]+(?:%source%|%year%|%group%|%audio%|%flags%|%format%|%regex_date%|%regex_date_monthname%|%language%[._)-])/iU';
	// Good for general Software releases (also Games)
	//const REGEX_TITLE_APP = '/^' . self::REGEX_TITLE . '[._\(-]+(?:' . self::REGEX_VERSION . '[._\(-]?\d|%language%|%flags%|%device%|%format%|%os%|%group%|%source%)/iU';
	const REGEX_TITLE_APP = '/^' . self::REGEX_TITLE . '[._(-]+(?:' . self::REGEX_VERSION . '|%device%|%disc%|%os%|%source%|%resolution%|%language%)[._)-]/iU'; // ungreedy
	// Bookware
	const REGEX_TITLE_BOOKWARE = '/^\w+[._-]' . self::REGEX_TITLE . '[._(-]bookware[._)-]/iU'; // ungreedy
	// Good for all kind of series (also Anime)
	const REGEX_TITLE_TV = '/^' . self::REGEX_TITLE . '[._-](?:' . self::REGEX_DISC . '|' . self::REGEX_EPISODE_TV . ')/iU';
	//const REGEX_TITLE_TV_EPISODE = '/' . self::REGEX_EPISODE_TV . '[._-](?:' . self::REGEX_TITLE . '[._\(-]+)?(?:%language%[._\)-]|%resolution%|%source%|%flags%|%format%)/iU';
	const REGEX_TITLE_TV_EPISODE = '/' . self::REGEX_EPISODE_TV . '[._-](?:' . self::REGEX_TITLE . '[._\(-]+)?\.+/iU'; // ungreedy
	const REGEX_TITLE_TV_DATE = '/^' . self::REGEX_TITLE . '[._\(-]+(?:%regex_date%|%year%)[._\)-]' . self::REGEX_TITLE . '?[._\(-]?(?:%language%[._\)-]|%resolution%|%source%|%flags%|%format%)/iU';
	// Good for XXX paysite releases
	const REGEX_TITLE_XXX = '/^' . self::REGEX_TITLE . '[._\(-]+(?:%year%|%language%[._\)-]|%flags%)/iU';
	//const REGEX_TITLE_XXX_DATE = '/^' . self::REGEX_TITLE . '[._-](?:\d+\.){3}' . self::REGEX_TITLE . '[._-](?:xxx|%language%)/iU';
	const REGEX_TITLE_XXX_DATE = '/^' . self::REGEX_TITLE . '[._\(-]+(?:%regex_date%|%regex_date_monthname%)[._\)-]+' . self::REGEX_TITLE . '[._\(-]+(?:%flags%|%language%[._\)-])/iU';
	// Extract software version
	const REGEX_VERSION_TEXT = '(?:v(?:ersione?)?|Updated?[._-]?v?|Build)';
	const REGEX_VERSION = self::REGEX_VERSION_TEXT . '[._-]?(\d[\d.]+[a-z\d]{0,3}(?![._-]gage))';
	const REGEX_VERSION_BOOKWARE = 'updated?[._-](\d{8})';


	// Type patterns.
	// Default type will be set to 'Movie' if no other matches.
	const TYPE = [
		// Audiobook
		'ABook' => 'a.*book',
		// Anime
		'Anime' => 'anime',
		// Software Sections
		'App' => [ 'app', 'apps-0day', 'pda', 'mobile' ],
		// Bookware
		'Bookware' => 'bookware',
		// Ebook
		'eBook' => 'book',
		// Font
		'Font' => 'font',
		// Game/Console Sections
		'Game' => [ 'GAME', 'D[SC]', 'G[BC]', 'NINTENDO', 'NSW', 'PLAYSTATION', 'PS[pvx\d]', 'XBOX', 'X360', 'WII' ],
		// Music Sections
		'Music' => [ 'mp3', 'flac', 'music', 'ringtones' ],
		// Music Video Sections
		'MusicVideo' => 'm(vid|dvd|bluray)',
		// TV Sections
		'TV' => 'tv',
		// Sports
		'Sports' => 'sport',
		// XXX Sections
		'XXX' => [ 'xxx', 'imgset', 'imageset', 'paysite' ],
		// Movie Sections
		'Movie' => [ 'movie', '(?!tv).*26[45]', 'bluray', 'dvdr', 'xvid', 'divx' ],
	];

	// Video/Audio source patterns
	const SOURCE = [
		// Bigger prio for music discs, so stuff in release title doesnt get parsed as source
		'MBluray' => 'MBLURAY',
		'MDVDR' => 'MDVDR?',
		'ABC' => 'ABC', // American Broadcasting Company (P2P)
		'Amazon' => [ 'AZ', 'AMZN', 'AmazonHD' ], // (P2P)
		'Amazon Freevee' => 'Freevee', // (P2P)
		'ATVP' => 'ATVP', // Apple TV
		'AUD' => 'AUD', // Audience Microphone
		'BBC iPlayer' => 'iP', // (P2P)
		'BDRip' => 'b[dr]+[._-]?rip',
		'BookMyShow' => 'BMS', // (P2P)
		'Bootleg' => '(?<!MBluRay.)(?:LIVE|\\d*cd)?.?BOOTLEG',
		'Cable' => 'cable.\d+', // Live show recorded from radio using cable
		'CAM' => '(?:new)?cam([._-]?rip)?',
		'CBS' => 'CBS', // CBS Corporation (P2P)
		'CD Album' => '\d*cda', // CD Album
		'CD EP' => '\d*cd.?ep',
		'CD Single' => [ 'cds', '(?:cd[._-]?)single' ], // CD Single
		'Comedy Central' => 'CC', // (P2P)
		'Console Disc' => [ 'xbox.*dvd[[:alnum:]]*', 'dvd[[:alnum:]]*.*xbox', 'cd.*xbox', 'pscd[[:alnum:]]*', 'PS2(?:.?d[vw]d[[:alnum:]]*|.?cd|.?rip)?', 'ps3.?bd[[:alnum:]]*', '(?:blue?ray).*ps3', 'xbox360.?dvd[[:alnum:]]*', 'Wii[iu]?(.?dvd[[:alnum:]]*)' ],
		'Crave' => 'CRAV', // (P2P)
		'Crunchyroll' => 'CR', // (P2P)
		'DAB' => 'dab', // Digital Audio Broadcast
		'DC Universe' => 'DCU', // (P2P)
		'DD' => 'dd(?![._-]?\d)', // Digital Download
		'DDC' => 'ddc', // Downloadable/Direct Digital Content
		'DAT Tape' => '\d*DAT', // Digital Audio Tape (after ddc, rare case)
		'Disney Plus' => [ 'DP', 'DSNP' ], // (P2P)
		'Disney Networks' => 'DSNY', // (P2P)
		'Discovery Plus' => 'DSCP', // (P2P)
		'DSR' => [ 'dsr', 'dth', 'dsr?[._-]?rip', 'sat[._-]?rip', 'dth[._-]?rip' ], // Digital satellite rip (DSR, also called SATRip or DTH)
		'DVB' => [ 'dvb[sct]?(?:[._-]?rip)?', 'dtv', 'digi?[._-]?rip' ],
		'DVDA' => '\d*dvd[_-]?a', // Audio DVD
		'DVDS' => 'dvd[_-]?s', // DVD Single
		'DVDRip' => '(?:r\d[._-])?dvd[._-]?rip(?:xxx)?',
		'EDTV' => 'EDTV(?:[._-]rip)?', // Enhanced-definition television
		'FM' => '\d*FM', // Analog Radio
		'Google Play' => 'GPLAY', // (P2P)
		'HBO Max' => [ 'HM', 'HMAX', 'HBOM', 'HBO[._-]Max' ], // (P2P)
		'HDCAM' => 'HDCAM',
		'HDDVD' => '\d*hd[\d._-]?dvd(?:r|rip)?',
		'HDRip' => [ 'hd[._-]?rip', 'hdlight', 'mhd' ],
		'HDTC' => 'HDTC(?:[._-]?rip)?', // High Definition Telecine
		'HDTV' => 'a?hd[._-]?tv(?:[._-]?(?:rip|src))?',
		'HLS' => 'HLS', // HTTP Live Streaming
		'Hotstar' => 'HTSR', // (P2P)
		'Hulu' => 'Hulu(?:UHD)?', // (P2P)
		'iTunes' => [ 'iT', 'iTunes(?:HD)?' ], // (P2P)
		'Lionsgate Play' => 'LGP', // Lionsgate Play (P2P)
		'LP' => '\d*lp', // Vinyl Album
		'Maxi CD' => [ 'cdm', 'mcd', 'maxi[._-]?single', '(?:cd[._-]?)?maxi' ], // CD Maxi
		'Maxi Single' => [ 'maxi[._-]?single|single[._-]?maxi', '(?<!cd[._-])maxi', '12[._-]?inch' ], // Maxi Single (Vinyl) / 12 inch
		'Movies Anywhere' => '(?<!DTS[._-]|HD[._-])MA', // (P2P)
		'MP3 CD' => '\d*mp3cd',
		'MTV Networks' => 'MTV', // (P2P)
		'Mubi' => 'MUBI', // (P2P)
		'NBC' => 'NBC', // National Broadcasting Company (P2P)
		'Netflix' => [ 'NF', 'NFLX', 'Netflix(?:HD)?' ], // (P2P)
		'Nintendo eShop' => 'eshop', // Nintendo eShop
		'Paramount Plus' => 'PMTP', // (P2P)
		'Peacock' => 'PCOK', // (P2P)
		'PDTV' => 'PDTV',
		'PPV' => 'PPV(?:[._-]?RIP)?', // Pay-per-view
		'PSN' => 'PSN', // Playstation Network
		'RAWRiP' => 'Rawrip', // Anime
		'SAT' => 'sat', // Analog Satellite
		'Scan' => 'scan',
		'Screener' => '(b[dr]+|bluray|dvd|vhs?|t[cs]|line)?.?(scr|screener)',
		'Showtime' => 'SHO', // (P2P)
		'SBD' => 'SBD', // Soundboard
		'Stan' => 'Stan(?:HD)?', // (P2P)
		'Stream' => 'stream',
		'Starz' => 'STA?R?Z', // (P2P)
		'TBS' => 'TBS', // Turner Broadcasting System
		'Telecine' => [ 'tc', 'telecine' ],
		'Telesync' => [ '(?:hd[._-])?ts(?:[._-]?src)?', 'telesync', 'pdvd' ], // ‘CAM’ video release with ‘Line’ audio synced to it.
		'UHDBD' => 'UHD[\d._-]?BD',
		'UHDTV' => 'A?UHD[._-]?TV',
		'VHS' => 'VHS(?!.?scr|.?screener)(?:.?rip)?',
		'VLS' => 'vls', // Vinyl Single
		'Vinyl' => [ '(Complete[._-])?Vinyl', '12inch' ],
		'VODRip' => [ 'VOD.?RIP', 'VODR' ],
		'Web Single' => '(?:web.single|single.web)', // Web single
		// If we have more than 1 source with WEB: the general WEB source needs to be the last one to be parsed
		'WEB' => 'WEB[._-]?(?!single)(?:tv|dl|u?hd|rip|cap|flac|mux)?',
		'WOW tv' => 'WOWTV', // (P2P)
		'XBLA' => 'XBLA', // Xbox Live Arcade
		'YouTube Red' => 'YTred', // (P2P)
		'MiniDisc' => [ 'md', 'minidisc' ], // Needs to be at the end, since some music releases has MD as source, but normally is MicDubbed for movies, so would wrongfully parse
		// Misc Fallback
		'Line' => 'line(?![._-]dubbed)',
		'CD' => [ '\d*cdr?\d*', 'cd.?(?:rom|rip)', '(retail.?|m)cd' ], // Other CD
		'DVD' => [ '\d+dvd[r\d]?', 'dvd' ], // Just normal DVD
		'Bluray' => [ '(?<!complete.|complete.uhd.)bl?u.?r[ae]y', '\d*bdr' ],
		'SDTV' => '(?:sd)?tv(?:[._-]?rip)?',
		'RiP' => 'rip', // If no other rip matches
		'BBC' => 'BBC', // British Broadcasting Company (P2P)
		// Parse EP source last, it's part of the name if other source given
		'EP' => 'EP',
	];

	// Video Encoding patterns
	// https://en.wikipedia.org/wiki/List_of_codecs#Video_compression_formats
	const FORMAT = [
		// Video formats
		'AVC' => 'AVC',
		'XViD' => 'XViD',
		// Type of Divx codecs:
		// SBC (Smart bitrate control), VKI (Variable Keyframe Intervals), MM4
		'DiVX' => [ '(?:DV)?DiVX\d*', 'VKI', 'SBC', 'MM4' ],
		'x264' => 'x\.?264',
		'x265' => 'x\.?265',
		'h264' => 'h\.?264',
		'h265' => 'h\.?265',
		'HEVC' => '(?:HDR10)?HEVC',
		'HEVC' => 'HEVC',
		'VP8' => 'VP8',
		'VP9' => 'VP9',
		'MP4' => 'MP4',
		'MPEG' => 'MPEG',
		'MPEG2' => 'MPEG2',
		'VCD' => '[m\d]?VCD',
		'CVD' => 'CVD',
		'CVCD' => 'D?CVCD', // Compressed Video CD
		'SVCD' => '[X\d]?SVC[de](?:rip)?',
		'VC1' => '(?:Blu.?ray.)?VC.?1',
		'WMV' => '(?:hd)?WMV',
		'MDVDR' => 'MDVDR?',
		'DVDR' => [ '(?:cn|mini)?DVD-?[R59]', 'complete.(?:pal|ntsc)|(?:pal|ntsc).complete' ],
		'MBluray' => '(Complete.?)?MBLURAY',
		'Bluray' => [ '(complete.?)?bluray', 'bd\d+' ],
		'MViD' => 'MViD',
		// Ebook formats
		'AZW' => 'AZW',
		'Comic Book Archive' => 'CB[artz7]',
		'CHM' => 'CHM',
		'ePUB' => 'EPUB',
		'Hybrid' => 'HYBRID',
		'LIT' => 'LIT',
		'MOBI' => 'MOBI',
		'PDB' => 'PDB',
		'PDF' => 'PDF',
		// Music formats
		'DAISY' => 'DAISY', // Audiobook
		'FLAC' => '(?:WEB.?)?FLAC',
		'KONTAKT' => 'KONTAKT',
		'MP3' => 'MP3',
		'WAV' => 'WAV',
		'3GP' => '3gp',
		// Software format
		'ISO' => '(?:Bootable.)?ISO',
		// Font format
		'CrossPlatform' => 'Cross(?:Format|Platform)',
		'OpenType' => 'Open.?Type',
		'TrueType' => 'True.?Type',
		// Software/Game format
		'Java Platform, Micro Edition' => 'j2me(?:v\d*)?',
		'Java' => 'JAVA',
		// Misc
		'Multiformat' => 'MULTIFORMAT'
	];

	// Video resolution patterns
	const RESOLUTION = [
		'480p' => '480p',
		'576p' => '576p',
		'720p' => '720p',
		'1080i' => '1080i',
		'1080p' => '1080p',
		'1920p' => '1920p',
		'2160p' => '2160p',
		'2700p' => '2700p',
		'2880p' => '2880p',
		'3072p' => '3072p',
		'3160p' => '3160p',
		'3600p' => '3600p',
		'4320p' => '4320p',
		'UHD' => 'UHD',
		'Upscaled UHD' => 'UpsUHD',
		'NTSC' => 'NTSC',	// = 480p
		'PAL' => 'PAL',		// = 576p
		'SD' => 'SD',
	];

	// Audio quality patterns
	const AUDIO = [
		'10BIT' => '10B(?:IT)?',
		'16BIT' => '16B(?:IT)?',
		'24BIT' => '24B(?:IT)?',
		'44K' => '44kHz',
		'48K' => '48kHz',
		'96K' => '96KHZ',
		'160K' => '16\dk(?:bps)?',
		'176K' => '176khz',
		'192K' => '19\dk(?:bps)?',
		'AAC' => 'AAC(?:\d)*',
		'AC3' => 'AC3(?:dub|dubbed|MD)?',
		'AC3D' => 'AC3D',
		'EAC3' => 'EAC3',
		'EAC3D' => 'EAC3D',
		'Dolby Atmos' => 'ATMOS',
		'Dolby Digital' => [ 'DOLBY.?DIGITAL', 'dd[^p]?\d+' ],
		'Dolby Digital Plus' => [ 'DOLBY.?DIGITAL', 'ddp.?\d' ],
		'Dolby Digital Plus, Dolby Atmos' => 'ddpa.?\d',
		'Dolby trueHD' => '(?:Dolby)?[._-]?trueHD',
		'DTS' => 'DTSD?(?!.?ES|.?HD|.?MA)[._-]?\d*',
		'DTS-ES' => 'DTS.?ES(?:.?Discrete)?',
		'DTS-HD' => 'DTS.?(?!MA)HD(?!.?MA)',
		'DTS-HD MA' => [ 'DTS.?HD.?MA', 'DTS.?MAD?' ],
		'DTS:X' => 'DTS[._-]?X',
		'OGG' => 'OGG',
		// Channels
		'2.0' => [ 'd+2[._-]?0', '\w*(?<!v[._-]|v)2[._-]0', '2ch' ],
		'2.1' => [ 'd+2[._-]?1', '\w*(?<!v[._-]|v)2[._-]1' ],
		'3.1' => [ 'd+3[._-]?1', '\w*(?<!v[._-]|v)3[._-]1' ],
		'5.1' => [ 'd+5[._-]?1', '\w*(?<!v[._-]|v)5[._-]1(?:ch)?' ],
		'7.1' => [ 'd+7[._-]?1', '\w*(?<!v[._-]|v)7[._-]1' ],
		'7.2' => [ 'd+7[._-]?2', '\w*(?<!v[._-]|v)7[._-]2' ],
		'9.1' => [ 'd+9[._-]?1', '\w*(?<!v[._-]|v)9[._-]1' ],
		// Misc
		'Dual Audio' => '(Dual[._-]|2)Audio',
		'Tripple Audio' => '(Tri|3)Audio'
	];

	// Game Console patterns
	const DEVICE = [
		'3DO' => '3DO',
		//'Bandai WonderSwan' => 'WS',
		'Bandai WonderSwan Color' => 'WSC',
		'Commodore Amiga' => 'AMIGA',
		'Commodore Amiga CD32' => 'CD32',
		'Commodore C64' => 'C64',
		'Commodore C264' => 'C264',
		'Nintendo Entertainment System' => 'NES',
		'Super Nintendo Entertainment System' => 'SNES',
		'Nintendo GameBoy' => [ 'GB', 'GAMEBOY' ],
		'Nintendo GameBoy Color' => 'GBC',
		'Nintendo GameBoy Advanced' => 'GBA',
		'Nintendo Gamecube' => [ 'N?GCN?', 'GAMECUBE' ],
		'Nintendo iQue Player' => 'iQP',
		'Nintendo Switch' => 'NSW',
		'NEC PC Engine' => 'PCECD',
		'Nokia N-Gage' => '(?:nokia[._-])?n[._-]?gage(?:[._-]qd)?',
		'Playstation' => 'PS[X1]?(cd)?',
		'Playstation 2' => '(?:jap|\d)?PS2(?:.?[ed][vw]d[[:alnum:]]*|.?cd[[:alnum:]]*|.?rip|.?hdd)?',
		'Playstation 3' => 'PS3(?:.?bd[[:alnum:]]*)?',
		'Playstation 4' => 'PS4',
		'Playstation 5' => 'PS5',
		'Playstation Portable' => 'PSP',
		'Playstation Vita' => 'PSV',
		'Pocket PC' => 'PPC\d*',
		'Sega Dreamcast' => [ 'DC$', 'DREAMCAST' ],
		'Sega Mega CD' => 'MEGACD',
		'Sega Mega Drive' => 'SMD',
		'Sega Saturn' => 'SATURN$',
		'Tiger Telematics Gizmondo' => 'GIZMONDO',
		'VTech V.Flash' => 'VVD',
		'Microsoft Xbox' => 'xbox(?:.?dvdr?i?p?\d?|rip|full|cd|manual|pack)?',
		'Microsoft Xbox One' => 'XBOXONE',
		'Microsoft Xbox360' => [ 'XBOX.?360(?:.?dvd)?', 'X360' ],
		// Higher prio (last has higher prio) for Wii + NDS, because of virtual console and release titles containing older consoles
		'Nintendo DS' => '3?DS(?:.dsi)?',
		'Nintendo 3DS' => '(?:vc[._](?:\w+[._])?|new|n)?3DS(?!.max|max|.max\d|max\d)(?:ware)?',
		'Nintendo WII' => '(?:vc[._](?:\w+[._])?)?Wiii?(.?dvd[r\d]?|clone|ware)?', 
		'Nintendo WII-U' => '(?:vc[._](?:\w+[._])?)?WII[._-]?U',
	];

	// Operating System patterns for Software/Game releases
	const OS = [
		'IBM AIX' => 'AIX', // Advanced Interactive eXecutive
		'Android' => 'Android',
		'BlackBerry' => 'Blackberry',
		'BSD' => '(?:Free|Net|Open)?BSD',
		'HP-UX' => 'HPUX', // Hewlett Packard Unix
		'iOS' => [ 'iOS', 'iPhone' ],
		'Linux' => 'Linux(?:es)?',
		'macOS' => [ 'mac.(retail|iso|all|regged|incl|french|german|multi\w+)', 'mac$', 'ma?c[._-]?os[x\d]?', 'osx' ],
		'PalmOS' => 'Palm[._-]?OS\d*',
		'Solaris' => '(Open)?Solaris',
		'SunOS' => 'SunOS',
		'Symbian' => 'Symbian(?:OS\d*[._-]?\d*)?',
		'Ubuntu' => 'Ubuntu',
		'Unix'	=> 'Unix(All)?',
		'WebOS' => 'WebOS',
		// Found these hillarious (but rule conform) windows tags for software releases:
		// win9xnt2000 / WinNT2kXPvista / Win2kXP2k3Vista / winxp98nt2kse / win2kxpvista / Win2KXP2003Vista / WinXP2k3Vista2k8
		'Windows' => 'win(?:[.]?(?:[\d]+[\dxk]?|nt|all|dows|x[xp]|vista|[msp]e)){1,6}',
		'Windows CE' => 'wince',
		'Windows Mobile' => 'wm\d+([._-]?se)?',
	];

	// Release language + language code patterns
	// https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
	const LANGUAGES = [
		'am' => 'Amharic',
		'ar' => 'Arabic',
		'az' => 'Azerbaijani',
		'bg' => 'Bulgarian',
		'bs' => 'Bosnian',
		'ch' => [ 'Swiss', 'CH' ],
		'cs' => [ 'Czech', 'CZ' ],
		'cy' => 'Welsh',
		'de' => [ 'German', 'GER', 'DE' ],
		'dk' => [ 'Danish', 'DK' ],
		'el' => [ 'Greek', 'GR' ],
		'en' => [ 'English', 'VOA', 'USA?', 'AUS', 'UK', 'GBR', 'ENG' ],
		'es' => [ 'Spanish', 'Espanol', 'ES', 'SPA', 'Latin.Spanish' ],
		'et' => [ 'Estonian', 'EE' ],
		'fa' => [ 'Persian', 'Iranian', 'IR' ],
		'fi' => [ 'Finnish', 'FIN?' ],
		'fil' => 'Filipino',
		'fr' => [ 'French', 'Fran[cç]ais', 'TRUEFRENCH', 'VFF' ],
		'ga' => 'Irish',
		'he' => 'Hebrew',
		'hi' => 'Hindi',
		'hr' => 'Croatian',
		'ht' => [ 'Creole', 'Haitian' ],
		'hu' => 'Hungarian',
		'id' => 'Indonesian',
		'is' => [ 'Icelandic', 'ICE' ],
		'it' => [ 'Italian', 'ITA?' ],
		'jp' => [ 'Japanese', 'JA?PN?' ],
		'kk' => 'Kazakh',
		'ko' => [ 'Korean', 'KOR' ],
		'km' => 'Cambodian',
		'lo' => 'Laotian',
		'lt' => [ 'Lithuanian', 'LIT' ],
		'lv' => 'Latvian',
		'mi' => 'Maori',
		'mk' => 'Macedonian',
		'ms' => [ 'Malay', 'Malaysian' ],
		'nl' => [ 'Dutch', 'HOL', 'NL', 'Flemish', 'FL' ],
		'no' => [ 'Norwegian', 'NOR?(?![._-]?\d+)' ],
		'ps' => 'Pashto',
		'pl' => [ 'Polish', 'PO?L' ],
		'pt' => [ '(?<!brazilian.)Portuguese', 'PT' ],
		'pt-BR' => [ 'Brazilian(?:.portuguese)?', 'BR' ],
		'ro' => 'Romanian',
		'ru' => [ 'Russian', 'RU' ],
		'sk' => [ 'Slovak', 'SK', 'SLO', 'Slovenian', 'SI' ],
		'sv' => [ 'Swedish', 'SW?E' ],
		'sw' => 'Swahili',
		'tg' => 'Tajik',
		'th' => 'Thai',
		'tl' => 'Tagalog',
		'tr' => [ 'Turkish', 'Turk', 'TR' ],
		'uk' => 'Ukrainian',
		'vi' => 'Vietnamese',
		'zh' => [ 'Chinese', 'CH[ST]' ],
		// Misc
		'multi' => [ 'Multilingual', 'Multi.?(?:languages?|lang|lingual|\d*)?', 'EURO?P?A?E?', '(?<!WEB.)[MD]L', 'DUAL(?!.Audio)' ],
		'nordic' => [ 'Nordic', 'SCANDiNAViAN' ]

	];

	// Release flags patterns
	const FLAGS = [
		'3D' => '3D',
		'Abridged' => [ 'ABRIDGED', 'gekuerzte?(?:.(?:fassung|lesung))' ], // Audiobook
		'Addon' => 'ADDON', // software
		'Anime' => 'ANiME',
		'ARM' => 'ARM', // software
		'Audiopack' => 'Audio.?pack', // Only audio releases for movies
		'Beta' => 'BETA', // software
		'Boxset' => 'BOXSET',
		'Chapterfix' => 'CHAPTER.?FIX', // Disc
		'Cheats' => 'Cheats', // games
		'Chrono' => 'CHRONO',
		'Colorized' => 'COLORIZED',
		'Comic' => 'COMICS?(.ebook(.\d+)?)?$',
		'Complete' => 'Complete',
		'Convert' => 'CONVERT',
		'Cover' => '(?:(?:CUST?OM|%language%|%format%|%source%|%resolution%|scans?|disc|ps[\dp]*|xbox\d*|gamecube|gc|unrated|\d*DVD\w+|hig?h?.?res|int|front|retail|\d+dpi|r\d+|original)[._-]?)COVERS?',
		'CPOP' => 'CPOP', // Chinese-pop
		'Incl. Crack' => [ 'CRACK.?ONLY', '(?:incl|working)[._-](?:[a-zA-Z]+[._-])?crack' ], // software
		'Cracked' => 'CRACKED', // software
		'Crackfix' => 'CRACK.?FIX', // software
		'Criterion' => 'CRITERION', // special movie rls
		'Cuefix' => 'Cue.?fix',
		'Digipack' => 'DIGIPAC?K?', // music
		'Directors Cut' => [ 'Directors?.?cut', 'dir[._-]?cut', 'DC' ],
		'DIRFiX' => 'DIR.?FIX',
		'DIZFiX' => 'DIZ.?FIX',
		'DLC' => '(?:incl.)?DLCS?(?!.?(?:Unlocker|Pack))?', // games
		'DOX' => 'D[O0]X',
		'Docu' => 'DO[CK]U?',
		'Dolby Vision' => [ 'DV', 'DoVi' ],
		'Dubbed' => [ '(?<!line.|line|mic.|mic|micro.|tv.)Dubbed', 'E.?Dubbed', '(?!over|thunder)[a-z]+dub' ],
		'Extended' => 'EXTENDED(?:.CUT|.Edition)?(?!.MIX)',
		'Final' => 'FINAL[._-]?(%language%|%flags%)?',
		'FiX' => '(?<!hot.|sample.|nfo.|rar.|dir.|crack.|sound.|track.|diz.|menu.)FiX(?:.?only)?',
		'Font' => '(Commercial.)?FONTS?',
		'Fontset' => '(Commercial.)?FONT.?SET',
		'Fullscreen' => 'FS', // Fullscreen
		'FSK' => 'FSK', // German rating system
		'Hardcoded Subtitles' => 'HC', // (P2P)
		'HDLIGHT' => 'HDLIGHT',
		'HDR' => 'HDR',
		'HDR10' => 'HDR10(?:hevc)?',
		'HDR10+' => 'HDR10(Plus|\+)',
		'Hentai' => 'Hentai',
		'HLG' => 'HLG', // Hybrid log-gamma (like HDR)
		'HOTFiX' => 'HOT.?FIX',
		'HOU' => 'HOU',
		'HR' => 'HRp?.(%flags%|%format%|%source%|%year%)', // High resolution
		'HSBS' => 'HS(?:BS)?',
		'Hybrid' => 'HYBRID',
		'Imageset' => [ '(?:Full[._-]?)?(?:IMA?GG?E?|photo|picture|pic|foto).?SETS?', 'pic.?xxx' ],
		'IMAX' => 'IMAX',
		'Internal' => 'iNT(ERNAL)?',
		'IVTC' => 'IVTC', // Inverce telecine
		'JAV' => 'JAV', // Japanese Adult Video
		'KEY' => 'GENERIC.?KEY',
		'KEYGEN' => [ '(?:Incl.)?KEY(?:GEN(?:ERATOR)?|MAKER)(?:.only)?', 'KEYFILE.?MAKER' ],
		'Intel' => 'INTEL',
		'Line dubbed' => [ 'ld', 'line.?dubbed' ],
		'Limited' => 'LIMITED',
		'M2TS' => 'M2TS', // Video container
		'Magazine' => 'MAG(AZINE)?',
		'Menufix' => 'MENU.?FIX',
		'Micro dubbed' => [ '(?:ac3)?md', 'mic(ro)?.?dubbed' ],
		'MIPS' => 'MIPS', // software (MIPS CPU)
		'New' => 'New[._-](%format%|%language%|%source%|%resolution%)',
		'NFOFiX' => 'NFO.?FiX',
		'OAR' => 'OAR', // Original Aspect Ratio
		'OVA' => 'O[AV]+\d*', // Original Video Anime/Original Anime Video
		'OAD' => 'OAD', // Original Anime DVD
		'ONA' => 'OMA', // Original Net Animation
		'OEM' => 'OEM',
		'OST' => 'OST', // music
		//'PACK' => 'PACK',
		'Incl. Patch' => [ '(?:incl.)?(?:[a-z]+[._-])?patch(?:ed)?(?:[._-]only)?', 'no[a-zA-Z]+[._-]patch(?:ed)?(?:[._-]only)' ], // software
		'Patchfix' => 'patchfix', // Software
		'Paysite' => 'PAYSITE', // xxx
		'Portable' => 'Portable', // Software
		'Preair' => 'PREAIR',
		'Proper' => '(?:REAL)?PROPER',
		'Promo' => 'PROMO',
		'Prooffix' => 'PROOF.?FIX',
		'Rated' => 'RATED',
		'RARFix' => 'RARFIX',
		'READNFO' => 'READ.?NFO',
		'Redump' => '(?:introfree.|new.)?REDUMP(?:.no.intro)?', // Games, GBA
		'Refill' => 'Refill',
		'Reissue' => 'REISSUE',	// music
		'Regged' => 'REGGED', // software
		'Regraded' => 'regraded', // Movie (p2p)
		'Remastered' => 'REMASTERED',
		'Remux' => 'REMUX',
		'Repack' => '(working.)?REPACK',
		'RERiP' => 're.?rip',
		'Restored' => 'RESTORED',
		'Retail' => 'RETAIL',
		'Ringtone' => 'rtones?',
		'Samplefix' => 'SAMPLE.?FIX',
		'Scrubbed' => 'scrubbed', // "scrubbing" refers to removing garbage or empty data from a game dump
		'SDR' => 'SDR',
		'Serial' => 'SERIAL(?!.Killer)?', // Software
		'SFVFix' => 'SFV.?FIX',
		'SH3' => 'SH3', // software (SH3 CPU)
		'Sizefix' => 'size.?fixed?',
		'Soundfix' => 'SOUNDFIX',
		'Special Edition' => [ 'SE(?![._-]\d*)', 'SPECIAL.EXTENDED.EDITION' ],
		'STV' => 'STV', // Straight To Video (never released in theaters)
		// https://ccm.net/sound-image/tv-video/2227-what-is-vostfr/
		'Subbed' => [ '[a-zA-Z]*SUB(?:BED|s)?', '(?:vo.?|fan.?)?SUB(?!pack)[._-]?\w+', '(c|custom).SUB(?:BED|s)', '(ST.?|VOS.?|VO.?ST.?)(FR[EA]?)?' ],
		'Subfix' => 'SUB.?FIX',
		'Subpack' => '(custom.|vob.)?\\w*sub?.?pack',
		'Superbit' => 'Superbit',	// https://de.wikipedia.org/wiki/Superbit
		'Syncfix' => 'SYNC.?FIX', // Video AUdio
		'Theatrical' => 'THEATRICAL',
		'Trackfix' => 'TRACK.?FiX', // Music
		'Trailer' => 'TRAILER',
		'TRAiNER' => 'Trainer(?!.XXX)(?:.(?:%flags%))?',
		'Tutorial' => 'TUT(?:ORIALS?)?',
		'TV Dubbed' => 'tv.?dubbed',
		'Unabridged' => [ 'UNABRIDGED', 'Ungekuerzt' ], // Audiobook
		'Uncensored' => 'UNCENSORED',
		'Uncut' => 'UNCUT',
		'Unlicensed' => 'UNLiCENSED',
		'Unrated' => 'UNRATED',
		'Untouched' => 'UNTOUCHED',
		'USK' => 'USK', // German rating system
		'Update' => '(WITH.)?UPDATE',
		'V1' => 'V1.(%format%|%language%|%source%|%resolution%)',
		'V2' => 'V2.(%format%|%language%|%source%|%resolution%)',
		'V3' => 'V3.(%format%|%language%|%source%|%resolution%)',
		'Vertical' => 'Vertical.(hrp?|xxx|%format%|%source%|%resolution%)',
		'VR' => 'VR', // Virtual reality
		'VR180' => 'VR180',
		'Workprint' => [ 'WORKPRINT', 'WP' ],
		'Widescreen' => [ 'widescreen', 'WS' ], // Widescreen
		'x64' => 'x64', // software
		'x86' => 'x86', // software
		'XSCale' => 'Xscale', // software
		'XXX' => 'XXX(?!.radio)'
	];

	// Format: DE + EN + NL / FR / IT / ES
	const MONTHS = [
		1 => 'Januar[iy]?|Janvier|Gennaio|Enero|Jan',
		2 => 'Februar[iy]?|Fevrier|Febbraio|Febrero|Feb',
		3 => 'Maerz|March|Moart|Mars|Marzo|Mar',
		4 => 'A[bpv]rile?|Apr',
		5 => 'M[ae][iy]|Maggio|Mayo',
		6 => 'Jun[ie]o?|Juin|Giugno|Jun',
		7 => 'Jul[iy]o?|Juillet|Luglio|Jul',
		8 => 'August|Aout|Agosto|Augustus|Aug',
		9 => 'Septemb[er][er]|Settembre|Septiembre|Sep',
		10 => 'O[ck]tob[er][er]|Ottobre|Octubre|Oct',
		11 => 'Novi?emb[er][er]|Nov',
		12 => 'D[ei][cz]i?emb[er][er]|Dec'
	];

	// All different Sports
	const SPORTS = [
		// Football
		'a-league',			// Australia
		'Allsvenskan\.(U\d+\.)?\d{4}',		// Sweden
		'Premier.?League',	// England (also darts)
		'La.?Liga.(santander.)?\d{2,4}',	// Spain
		'Joan.Gamper.Trophy',				// Spain (Barcelona)
		'Copa.del.rey',						// Spain Cup
		'Supercopa',						// Spain
		'(?:fussball.\d.|\d.)?Bundesliga\.\d{4}',	// Germany
		'Fussball.Laenderspiel.\d+', // German international friendly
		'Fussball.dfl.supercup', 	// German international friendly
		'dfb.pokal.\d{4}',			// Germany Cup
		'Eredivisie',		// Netherlands
		'Ligue.?1',					// France
		'Coupe.De.France.\d{2,4}',	// France Cup
		'Serie.?A',			// Italy
		'Coppa.Italia',		// Italy Cup
		'Suppercoppa',		// Italy
		'FA.?Cup',			// England
		'(?:fussball.)?EPL',					// England
		'EFL\d?.(?:\d{1,4}|cup|championship)',	// England
		'B?WSL',			// England women
		'Arnold.Clark.Cup', // England women
		'SPFL',				// Scottland
		'Viaplay.Cup',		// Scottland
		'Scottish.cup',		// Scottland Cup
		'Fodbold',			// Denmark
		'MLS.\d{4}',		// USA
		'NWSL.\d{4}',		// USA women
		'CSL.\\d+',			// China
		'fifa.(?:world.cup|fotbolls|futsal|wm|U\d+).\d{4}', // International
		'fifa.(?:world.cup.(?:U\d+.)?)?womens.\d{4}',		// International women
		'(?:international.(?:friendly.)?)?football.(?:australia|womens|sydney|friendly|ligue1|serie.a|uefa|conference|league)', // International
		'womens.international',
		'(?:womens.)?UEFA.(champions|cl|cup|euro|europa|super.?cup|Bajnokok|european|pokal|futsal|el|under\d+|u\d+|womens|em|youth.league|\d{4})', // European
		'UCL.\d+',			// UEFA Champions League
		'UEL.\d{4}', 'Europa.league', // UEFA Europa league
		'concacaf',			// North and Middle America
		'conmebol',			// South America
		'copa.?america',
		'CAF.Africa.Cup.Of.Nations', // African
		'Saudi.pro.league',
		'afc.asian',		// Asian
		'match.of.the.day', 'pre.season.friendly',	// Misc football
		// Racing
		'Formul[ae].?[1234E].\d{4}', 'Formel.?[1234E].\d{4}', '(?:british.)?F\d.\d{4}.Grosser',
		'Superleague.Formula', 'Nascar.(?:cup|truck|xfinity|monster|raceday|camping|america|sprint|nextel|busch|\d{4})', '(?:ntt.)?Indycar\.(series|racing|\d{4})',
		'DTM.(\d{2,4}|spa|lauszitzring|gp\d+|\d+.lauf)', // Deutsche Tourenwagen Masters
		'DTC.\d{4}', // Danish Touringcar Championship
		'[ew]rc[._-](?:\d{4})', 
		'(?:[ew]rc|rx)[._-]fia[._-](?:world|european).rally(?:cross)?.championship.\d{4}', // Rallye
		'Supercars.championship', 'V8.Supercars.\d{4}', 'Porsche.(Carrera|sprint|supercup)', 'Volkswagen.Racing.Cup', 'W.series.\d{4}',
		'IMSA', 'Nitro[.]RX', 'Extreme[.]e',
		// Moto racing
		'Moto.?(GP|\d).\d{4}',
		'A?SBK.(?:\d{4}|world)', 	// Australian Superbike
		'WSX', // Supercross
		'FIM[.](?:ewc|superbike|speedway|cev|isde|motocross|world)', // International Moto events
		// Cycling
		'Cycling.(?:volta|giro|tour|strade|paris|criterium|liege|fleche|amstel|la.vuelta)', // International
		'giro.d.italia', // Italy
		'la.vuelta.(?:a.espana.)?\d{4}', // Spain
		'tour.de.france.(?:femmes.)?\d{4}.stage.?\d+', // France
		'UCI',	// International
		// Rugby
		'(?:premiership.)?rugby.(?:cup.)?\d{4}',
		'(?:Super.|six.?nations.)?rugby.(world.cup|championship|pacific|aupiki|league.(?:test.match|anzak|challenge|nsw|four|toyota|trans|state|super|womens|international|world|\d{4}))',
		'International.rugby',
		'IPL.\d{4}',		 // India
		'NRL.(\d{4}|state)', // Australasian
		// Basketball
		'NBA.(?:\d{4}.)?(?:East|eastern|West|western|Finals|playoffs|\d{2}.\d{2})',
		'WNBA.\d{4}',	// USA women
		'Eurocup',		// Europe
		'Euroleague',
		'WNBL',			// Australia women
		'Fiba',			// International
		// Cricket
		'T20', 'BBL.\d{4}', 'ICC.Cricket',
		// American Football
		'NFL',
		// Wrestle
		'wwe',
		'aew.(?:collision|dynamite|dark|rampage)', 'New.Japan.Pro.Wrestling', 'NJPW', 'Game.changer.wrestling',
		// Fighting
		'(hbo.|uk.|frank.warren.)?boxing.\d{4}.\d{2}.\d{2}', 'Grand.Sumo', 'UFC.(\d+|on.(?:espn|abc|fo?x|fuel|versus)|fight.night)', 'showtime.championship.boxing', 'Bellator.(?:fighting|\d{2,4})', 'KSW[._-]\d{2,4}', 'Cage[.](?:fury|warriors)[.]\d{2,4}', 'PFL.\d{4}', 'Invicta.fc', 'Enfusion(?:[.]ece|[.]live)?[.]\d{2,4}',
		// Icehockey
		'NHL\.(?:\d{4}|stanley.cup|playoffs|all.stars?|pre.?season|awards)',
		'Elitserien', 	// Swedish icehockey
		'IIHF', // International Icehoceky
		// Baseball
		'MLB.(?:\d{4}|spring|(?:world|championship|division).series|pre.?season|playoffs|ws|alcs|wild.card)',
		// Olympics
		'(\w+.)?(?:winter|summer).(paralympics|olympics)',
		'\w+.(paralympics|olympics)',
		// Tennis
		'wimbledon.(?:tennis.)?\d{4}', 'us.open.\d{4}', '([aw]tp.|wta.)french.open(?:.tennis)?.\d{4}', 'australian.open', 'WTA.\d{4}',
		// Pool
		'[[:alnum:]]+.open.pool.championship.\d{4}',
		'world.pool.masters',
		'mosconi.cup',
		// MISC
		'Biathlon.(?:\d{4}|WC|VM|EM|Weltcup|world)',
		'^pdc[.](?:.*darts|players.championship)', // Darts
		// eSports
		'LPL.PRO',
		// World cup of whatever
		'World.cup'
	];

	// Bookware/elearning platforms
	const BOOKWARE = [
		'3DMotive', 'Academy.Moz', 'A.?Cloud.?Guru', 'Actualtests', 'Addison.?Wesley', 'AppDev', 'APRESS', 'Artstation', 'Ask.?video', 'Bassgorilla', 'Career.Academy', 'CBT.?Nuggets', 'CG.?(Circuit|workshops?|cookie)?', 'Cloud.academy', 'Codeschool.com', 'CreativeLive', 'Digital.?tutors', 'EC.?COUNCIL', 'Educator', 'Egghead.?io', 'Fravo', 'Foundation.patreon', 'Fxp.?HD', 'Galileo.(press|computing|design)', 'Groove3', 'Gumroad', 'ICOLLEGE', 'INE(.com)?', 'Infinite.?skills', 'INTELLEZY', 'kelbyone', 'Kelby.?(one)?Training', 'La.?formation', 'Learnable.com', 'Learnflash(.com)?', 'Learnnowonline', 'LinkedIn(?:.learning)?', 'Linux.Academy', 'Linux.?CBT', 'Lynda(?:.com)?', 'MacProVideo.com', 'Manning', 'Masterclass', 'Mixwiththemasters', 'M.O.C', 'Mycodeteacher.com', 'Open.?Classrooms', 'OREILLY', 'ostraining', 'packt', 'Pass4sure', 'PEACHPIT', 'PEARSON(.it)?', 'PluralSight', 'Pragmatic(.?ai)?', 'PrepLogic', 'PSD.?tutorials', 'Retouching.Academy', 'RW', 'SCO', 'Sitepoint.com', 'Skillshare', 'skillfeed', 'Skillsoft', 'Skylines.?Academy', 'Sonic.academy', 'Spin.?academy', 'Stone.?River.?eLearning', 'Syngress', 'Technics.?Publications?', '(team)?treehouse', 'Testking', 'Testnow', 'The.?Gnomon.?Workshop', 'Trainsignal', 'Train.?simple', 'Total.?Training', 'Tutsplus', 'TVI', 'Video2Brain', 'Videomaker.com', 'VTC(.com)?', 'VueMastery', 'Unix.?CBT', 'Wintellect.?NOW', 'Winstructor', 'Udemy(?:.com)?'
	];

	const GROUPS_GAMES = [
		'0x0007', '0x0815', '1C', 'ABSiSO', 'ACTiVATED', 'ADDONiA', 'ALiAS', 'ANOMALY', 'AUGETY', 'AVENGED', 'BACKLASH', 'bADkARMA', 'Bamboocha', 'BAT', 'BAZOOKA', 'BFHiSO', 'BiTE', 'BLASTCiTY', 'BReWErS', 'BREWS', 'BREWZ', 'CiFE', 'CLONECD', 'CLS', 'CODEX', 'COGENT', 'CUBiC', 'CXZiSO', 'DARKSiDERS', 'DARKZER0', 'DELiGHT', 'DEViANCE', 'DINOByTES', 'DOGE', 'DVN', 'DVNiSO', 'DYNAMIX', 'ENiGMA', 'FANiSO', 'FAS', 'FASiSO', 'FASDOX', 'FCKDRM', 'FLT', 'FLTDOX', 'GENESIS', 'gimpsRus', 'GMiSO', 'GOW', 'GREENPEACE', 'HATRED', 'HBD', 'HEiST', 'HI2U', 'HOODLUM', 'HR', 'HYBRID', 'I_KnoW', 'iMMERSiON', 'iNLAWS', 'iTWINS', 'JAGDOX', 'JAGUAR', 'LiGHTFORCE', 'LUMA', 'MONEV', 'MYSTERY', 'MYTH', 'NiiNTENDO', 'NNSSWW', 'OUTLAWS', 'PiKMiN', 'PiMoCK', 'PiZZA', 'PiZZADOX', 'PLAZA', 'POSTMORTEM', 'PRELUDE', 'PROPHET', 'PS5B', 'PUSSYCAT', 'PWZ', 'TENOKE', 'TENOKE1', 'THG', 'TiNYiSO', 'TRSi', 'TSC', 'RELOADED', 'RAZOR', 'Razor1911', 'RAZORCD', 'RazorDOX', 'ReVOLVeR', 'RiTUEL', 'RUNE', 'SCRUBS', 'SiLENTGATE', 'SiMPLEX', 'SKIDROW', 'SMACKs', 'Souldrinker', 'SPLATTER', 'SPLATTERKiNGS', 'STEAMPUNKS', 'STRANGE', 'SUXXORS', 'TDUJAM', 'TECHNiC', 'TEDOX', 'TNT', 'VACE', 'VENGEANCE', 'VENOM', 'ViTALiTY', 'VREX', 'Unleashed', 'YOUCANTNUKE', 'ZEKE'
	];

	const GROUPS_APPS = [
		'ACME', 'AGAiN', 'AMPED', 'BEAN', 'BLiZZARD', 'BLZiSO', 'BRD', 'BTCR', 'BTCRiSO', 'CADZ', 'CAFE', 'CaviaR', 'CNC', 'CORE', 'CRD', 'CROSSFiRE', 'CYGiSO', 'DAMN', 'DARKLEASH', 'DIGERATI', 'DiSTiNCT', 'DSi', 'dT', 'DVT', 'DVTiSO', 'EAT', 'ECLiPSE', 'ECU', 'ECZ', 'EMBRACE', 'ENFUSiA', 'EPS', 'EQUiNOX', 'EXPANSION', 'EXPLOSiON', 'F4CG', 'FALLEN', 'FCN', 'HERiTAGE', 'HOTiSO', 'iNDiSO', 'iNFECTED', 'iNTENSiON', 'ISO', 'ISOBelix', 'iSOTOPE', 'LaTeX', 'LAXiTY', 'LND', 'LUCiD', 'Lz0', 'Lz0PDA', 'MAS', 'MASCHiNE', 'MAGNETiC', 'MAGNiTUDE', 'MESMERiZE', 'MIDNIGHT', 'MSGPDA', 'NGEN', 'NiTROUS', 'ORiON', 'PARADOX', 'PFT', 'PGC', 'PH', 'PROPHECY', 'RENEGADE', 'rG', 'RINDVIEH', 'RiSE', 'RLTS', 'ROR', 'RORiSO', 'SCOTCH', 'SCRiPTMAFiA', 'SONiTUS', 'SoSISO', 'SSG', 'SUBSTANCE', 'TBE', 'TE', 'TFTDOX', 'TFTISO', 'TMG', 'TNO', 'TSZ', 'TZ7iSO', 'ViRiLiTY', 'UCF', 'UnderPl', 'XFORCE', 'XiSO', 'ZWT', 'ZWTiSO', 'ZZGiSO'
	];

	// Put together some flag/format arrays for better type parsing.
	// Flags
	const FLAGS_MOVIE = [ 'Dubbed', 'AC3 Dubbed' , 'HDR', 'HDR10', 'HDR10+', 'IMAX', 'Line dubbed', 'Micro dubbed', 'THEATRICAL', 'UNCUT', 'Remux', 'Subbed', 'Directors Cut' ];
	const FLAGS_EBOOK = [ 'Magazine', 'Comic', 'ePUB' ];
	const FLAGS_MUSIC = [ 'CPOP', 'OST' ];
	const FLAGS_APPS = [ 'Cracked', 'Regged', 'KEYGEN', 'Incl. Patch', 'Crackfix', 'ISO', 'ARM', 'Intel', 'x86', 'x64', 'Portable', 'Patchfix' ];
	const FLAGS_GAMES = [ 'DLC', 'TRAiNER' ];
	const FLAGS_ANIME = [ 'Anime', 'OVA', 'ONA', 'OAD' ];
	const FLAGS_XXX = [ 'XXX', 'JAV', 'Imageset', 'Hentai' ];
	const FLAGS_VIDEO = [ 'IVTC' ];
	// Formats
	const FORMATS_VIDEO = [ 'AVC', 'VCD', 'SVCD', 'CVCD', 'XViD', 'DiVX', 'x264', 'x265', 'h264', 'h265', 'HEVC', 'MP4', 'MPEG', 'MPEG2', 'VC1', 'WMV' ];
	const FORMATS_MUSIC = [ 'FLAC', 'KONTAKT', 'MP3', 'OGG', 'WAV' ];
	const FORMATS_MVID = [ 'MBluray', 'MDVDR', 'MViD' ];
	// Sources
	const SOURCES_GAMES = [ 'Console Disc', 'Nintendo eShop', 'XBLA', 'PSN' ];
	const SOURCES_MOVIES = [ 'Bluray', 'CAM', 'DVD', 'HDCAM', 'HDTC', 'Screener', 'Telecine', 'Telesync', 'UHDBD' ];
	const SOURCES_MUSIC = [ 'AUD', 'CD Album', 'CD EP', 'CD Single', 'DAT Tape', 'DVDA', 'EP', 'FM', 'LP', 'Maxi CD', 'Maxi Single', 'MP3 CD', 'SBD', 'Tape', 'VLS', 'Vinyl', 'Web Single' ];
	const SOURCES_MVID = [ 'DDC', 'MBluray', 'MDVDR' ];
	const SOURCES_TV = [ 'ATVP', 'DSR', 'EDTV', 'HDTV', 'PDTV', 'SDTV', 'UHDTV', 'ABC', 'BBC iPlayer', 'CBS', 'Comedy Central', 'DC Universe', 'Discovery Plus', 'HBO Max', 'Hulu', 'MTV Networks', 'NBC', 'TBS' ];

}

/**
 * Polyfill functions.
 */

// PHP < 7.3
if ( !\function_exists( 'array_key_first' ) )
{
	function array_key_first( array $arr )
	{
		foreach( $arr as $key => $unused )
		{
			return $key;
		}
		return \null;
	}
}

if ( !\function_exists( 'array_key_last' ) )
{
	function array_key_last( array $array )
	{
		if ( !\is_array( $array ) || empty( $array ) )
		{
			return \null;
		}
		return \array_keys( $array )[ \count( $array ) - 1 ];
	}
}

// PHP < 8
if ( !\function_exists( 'str_contains' ) )
{
	function str_contains( string $haystack, string $needle )
	{
		return empty( $needle ) || \strpos( $haystack, $needle ) !== false;
	}
}
