<?php
namespace ReleaseParser;

// Include ReleaseParser
require_once __DIR__ . '/ReleaseParser.php';

/**
 * This is the main test file.
 * This are hand selected, at some point 'more complex' releases that need to pass the test.
 * 
 * @package ReleaseParser
 * @author Wellington Estevo
 * @version 1.5.0
 */

/**
 * Bulk tests. All have to pass.
 * 
 * @test
 */
function release_parser_test()
{
	// Some Bash color codes (looks better)
	$reset = "\33[0m";
	$bold = "\33[1m";
	$green = "\33[92m";
	$green = "\33[32m";
	$greenlight = "\33[92m";
	$greenlightbg = "\33[102m";
	$red = "\33[31m";
	$redlight = "\33[91m";
	$redbg = "\33[41m";
	$black = "\33[30m";
	$white = "\33[97m";
	$yellow = "\33[33m";

	// Time passed calc
	$start_time = \microtime( \true );

	echo \PHP_EOL . 'Starting ReleaseParser tests ...' . \PHP_EOL . \PHP_EOL;

	// All releases that needed to be tested.
	$tests = [

		// Game
		[
			'Game #1',
			new ReleaseParser( 'Diablo_II_Resurrected_Update_v1.0.0.3_incl_Offline_Crack_NSW-VENOM', 'games' ),
			'Title: Diablo II Resurrected / Group: VENOM / Flags: Incl. Crack, Update / Device: Nintendo Switch / Version: 1.0.0.3 / Type: Game'
		],
		[
			'Game #2 - SImple with device',
			new ReleaseParser( 'Madshot_NSW-VENOM', 'games' ),
			'Title: Madshot / Group: VENOM / Device: Nintendo Switch / Type: Game'
		],
		[
			'Game #3 - Console disc',
			new ReleaseParser( 'NFL_Madden_2007_USA_BLUERAY_PS3-PARADOX', 'PS3' ),
			'Title: NFL Madden 2007 / Group: PARADOX / Year: 2007 / Source: Console Disc / Device: Playstation 3 / Language: English / Type: Game'
		],
		[
			'Game #4',
			new ReleaseParser( 'Super_Smash_Bros_Brawl_DVD5_PAL_VC_SMD_Wii-WiiLeet', 'Wii' ),
			'Title: Super Smash Bros Brawl / Group: WiiLeet / Disc: 5 / Format: DVDR / Resolution: PAL / Device: Nintendo WII / Type: Game'
		],

		

		// Apps
		[
			'Apps #1',
			new ReleaseParser( 'RSP.OGG.Vorbis.Player.OCX.v2.5.0-Lz0', 'Apps' ),
			'Title: RSP OGG Vorbis Player OCX / Group: Lz0 / Version: 2.5.0 / Type: App'
		],
		[
			'Apps #2',
			new ReleaseParser( 'ActiveState.Visual.Python.for.VS.2003.v1.8.1.2082.WinNT2K.Incl.Keygenerator-TMG', 'Games' ),
			'Title: ActiveState Visual Python for VS 2003 / Group: TMG / Year: 2003 / Flags: KEYGEN / Os: Windows / Version: 1.8.1.2082 / Type: App'
		],
		[
			'Apps #3',
			new ReleaseParser( 'Aviano.Update.v1.03-ANOMALY', '0day' ),
			'Title: Aviano / Group: ANOMALY / Flags: Update / Version: 1.03 / Type: Game'
		],
		[
			'Apps #4',
			new ReleaseParser( 'QUIZWARE.PRACTICE.TESTS.FOR.COMPUTER.ASSOCIATES.CERTIFICATIONS.V4.84-JGT', 'ebook' ),
			'Title: Quizware Practice Tests For Computer Associates Certifications / Group: JGT / Version: 4.84 / Type: App'
		],
		[
			'Apps #5 - Movie Source and Audio in rls name',
			new ReleaseParser( 'SurCode.DVD.Professional.DTS.Encoder.v1.0.21.Retail-iNTENSiON', 'Apps' ),
			'Title: SurCode DVD Professional DTS Encoder / Group: iNTENSiON / Flags: Retail / Version: 1.0.21 / Type: App'
		],
		[
			'Apps #6 - OS in rls name',
			new ReleaseParser( 'Schweighofer.Win1A.Lohn.v23.10.4.0.German.WinALL.Incl.Keygen-BLiZZARD', 'Apps' ),
			'Title: Schweighofer Win1A Lohn / Group: BLiZZARD / Flags: KEYGEN / Os: Windows / Version: 23.10.4.0 / Language: German / Type: App'
		],
		[
			'Apps #7 - Remove OS from rls name',
			new ReleaseParser( 'Broforce.Forever.MacOS-I_KnoW', 'Pre' ),
			'Title: Broforce Forever / Group: I_KnoW / Os: macOS / Type: Game'
		],
		[
			'Apps #8 - Symbian App - dont falsely parse episode and season',
			new ReleaseParser( 'PocketTorch.AquaCalendar.V1.v1.2.N3650.NGAGE.SX1.S60.SymbianOS.READ.NFO.Cracked-aSxPDA', 'NGAGE' ),
			'Title: PocketTorch AquaCalendar / Group: aSxPDA / Flags: Cracked, READNFO / Device: Nokia N-Gage / Os: Symbian / Version: 1.v1 / Type: App'
		],
		[
			'Apps #9 - Win stuff',
			new ReleaseParser( 'TweakNow.PowerPack.2003.Professional.v1.2.Win.98.ME.2K.XP.RETAIL-iPA', 'APPS' ),
			'Title: TweakNow PowerPack 2003 Professional / Group: iPA / Year: 2003 / Flags: Retail / Os: Windows / Version: 1.2 / Type: App'
		],

		// Bookware
		[
			'Bookware #1',
			new ReleaseParser( 'PluralSight.Microsoft.Azure.Network.Engineer-secure.And.Monitor.Networks.Bookware-KNiSO', '0day' ),
			'Title: Microsoft Azure Network Engineer-secure And Monitor Networks / Group: KNiSO / Source: PluralSight / Type: Bookware'
		],
		[
			'Bookware #2 - With language',
			new ReleaseParser( 'UDEMY.Desarrollando.el.Sistema.SysCarteleria.v2.Visual.FoxPro.SPANISH.BOOKWARE-iLEARN', 'PRE' ),
			'Title: Desarrollando el Sistema SysCarteleria v2 Visual FoxPro / Group: iLEARN / Source: Udemy / Language: Spanish / Type: Bookware'
		],
		[
			'Bookware #3 - With version (update)',
			new ReleaseParser( 'PluralSight.Angular.Getting.Started.UPDATED.20220706.Bookware-KNiSO', 'PRE' ),
			'Title: Angular Getting Started / Group: KNiSO / Source: PluralSight / Version: 20220706 / Type: Bookware'
		],
		

		// Movies (usually a lot of flags for testing)
		[
			'Movies #1',
			new ReleaseParser( 'Harry.Potter.und.die.Kammer.des.Schreckens.TS.Line.Dubbed.German.INTERNAL.VCD.CD2.REPACK-TGSC', 'Apps' ),
			'Title: Harry Potter und die Kammer des Schreckens / Group: TGSC / Disc: 2 / Flags: Internal, Line dubbed, Repack / Source: Telesync / Format: VCD / Language: German / Type: Movie'
		],
		[
			'Movies #2',
			new ReleaseParser( 'Sweet.Home.Alabama.SCREENER.Line.Dubbed.German.VCD-TGSC', 'Screener' ),
			'Title: Sweet Home Alabama / Group: TGSC / Flags: Line dubbed / Source: Screener / Format: VCD / Language: German / Type: Movie'
		],
		[
			'Movies #3',
			new ReleaseParser( 'Die.Bourne.Verschwoerung.German.2004.INTERNAL.No.Bock.uff.Proper.READ.NFO.AC3.Dubbed.DL.DVDR-Cinemaniacs', 'DVDR' ),
			'Title: Die Bourne Verschwoerung / Group: Cinemaniacs / Year: 2004 / Flags: Dubbed, Internal, Proper, READNFO / Format: DVDR / Audio: AC3 / Language: German, Multilingual / Type: Movie'
		],
		[
			'Movies #4',
			new ReleaseParser( 'Gegen.den.Strom.2018.German.AC3D.DL.1080p.BluRay.x264-SAVASTANOS', 'X264' ),
			'Title: Gegen den Strom / Group: SAVASTANOS / Year: 2018 / Source: Bluray / Format: x264 / Resolution: 1080p / Audio: AC3D / Language: German, Multilingual / Type: Movie'
		],
		[
			'Movies #5 - Version in name: falsely parsing a version',
			new ReleaseParser( 'Burial.Ground.The.Nights.Of.Terror.1981.DUBBED.GRINDHOUSE.VERSION.1080P.BLURAY.X264-WATCHABLE', 'X264' ),
			'Title: Burial Ground The Nights Of Terror / Group: WATCHABLE / Year: 1981 / Flags: Dubbed / Source: Bluray / Format: x264 / Resolution: 1080p / Type: Movie'
		],
		[
			'Movies #6',
			new ReleaseParser( 'Batman.v.Superman.Dawn.of.Justice.2016.IMAX.German.DL.TrueHD.Atmos.DUBBED.2160p.UHD.BluRay.x265-GSG9', 'PRE' ),
			'Title: Batman v Superman Dawn of Justice / Group: GSG9 / Year: 2016 / Flags: Dubbed, IMAX / Source: Bluray / Format: x265 / Resolution: 2160p / Audio: Dolby Atmos, Dolby trueHD / Language: German, Multilingual / Type: Movie'
		],
		[
			'Movies #7 - lots of flags',
			new ReleaseParser( 'Wonder.Woman.1984.2020.IMAX.German.UHDBD.2160p.DV.HDR10.HEVC.TrueHD.DL.Remux-pmHD', '0DAY' ),
			'Title: Wonder Woman 1984 / Group: pmHD / Year: 2020 / Flags: Dolby Vision, HDR10, IMAX, Remux / Source: UHDBD / Format: HEVC / Resolution: 2160p / Audio: Dolby trueHD / Language: German, Multilingual / Type: Movie'
		],
		[
			'Movies #8 - Multiple Audio',
			new ReleaseParser( 'Cloudy.With.A.Chance.Of.Meatballs.2009.NORDIC.DTS-HD.DTS.AC3.NORDICSUBS.1080p.BluRay.x264-TUSAHD', 'X264' ),
			'Title: Cloudy With A Chance Of Meatballs / Group: TUSAHD / Year: 2009 / Flags: Subbed / Source: Bluray / Format: x264 / Resolution: 1080p / Audio: AC3, DTS, DTS-HD / Language: Nordic / Type: Movie'
		],
		[
			'Movies #9 - P2P lots of stuff',
			new ReleaseParser( 'Angel.Heart.1987.German.DTSMAD.5.1.DL.2160p.UHD.BluRay.HDR.DV.HEVC.Remux-HDSource', 'BLuray' ),
			'Title: Angel Heart / Group: HDSource / Year: 1987 / Flags: Dolby Vision, HDR, Remux / Source: Bluray / Format: HEVC / Resolution: 2160p / Audio: DTS-HD MA, 5.1 / Language: German, Multilingual / Type: Movie'
		],
		[
			'Movies #10 - Multiple lang codes',
			new ReleaseParser( 'Animals.United.2010.Audiopack.Dk.No.Fi.DVDRip.XviD-DiGiCo', 'XViD' ),
			'Title: Animals United / Group: DiGiCo / Year: 2010 / Flags: Audiopack / Source: DVDRip / Format: XViD / Language: Danish, Finnish, Norwegian / Type: Movie'
		],
		[
			'Movies #11 - No group at the end',
			new ReleaseParser( 'Intruders.Die.Aliens.Sind.Unter.Uns.1992.Uncut.German.AC3.DVDRiP.XviD', 'x265' ),
			'Title: Intruders Die Aliens Sind Unter Uns / Group: NOGRP / Year: 1992 / Flags: Uncut / Source: DVDRip / Format: XViD / Audio: AC3 / Language: German / Type: Movie'
		],
		[
			'Movies #12 - Flags in title, dont parse episode',
			new ReleaseParser( 'Der.Herr.Der.Ringe.Die.Gefaehrten.SPECIAL.EXTENDED.EDITION.2001.German.DL.1080p.BluRay.AVC.READ.NFO.PROPER-AVCBD', 'BLURAY-AVC' ),
			'Title: Der Herr Der Ringe Die Gefaehrten / Group: AVCBD / Year: 2001 / Flags: Extended, Proper, READNFO, Special Edition / Source: Bluray / Format: AVC / Resolution: 1080p / Language: German, Multilingual / Type: Movie'
		],
		[
			'Movies #13 - Special char in title',
			new ReleaseParser( 'Hansel.&.Gretel.Witch.Hunters..2013.DiRFiX.NTSC.MULTi.DVDR-FUTiL', 'PRE' ),
			'Title: Hansel & Gretel Witch Hunters / Group: FUTiL / Year: 2013 / Flags: DIRFiX / Format: DVDR / Resolution: NTSC / Language: Multilingual / Type: Movie'
		],
		[
			'Movies #14 - Don\'t falsely parse season',
			new ReleaseParser( 'V.H.S.94.2021.BluRay.1080p.DTS-HD.MA.5.1.AVC-GROUPNAME', 'PRE' ),
			'Title: V H S 94 / Group: GROUPNAME / Year: 2021 / Source: Bluray / Format: AVC / Resolution: 1080p / Audio: DTS-HD MA, 5.1 / Type: Movie'
		],
		[
			'Movies #15 - Don\'t falsely parse movie as tv show + special group chars',
			new ReleaseParser( 'Pay.the.Ghost.2015.1080p.HULU.WEB-DL.DDP.5.1.H.264-PiRaTeS[TGx]', 'PRE' ),
			'Title: Pay the Ghost / Group: PiRaTeS[TGx] / Year: 2015 / Source: Hulu / Resolution: 1080p / Audio: Dolby Digital Plus, 5.1 / Type: Movie'
		],

		// TV
		[
			'TV #1 - Multiple episodes: 01-02',
			new ReleaseParser( 'Full.Metal.Panic.Eps.01-02.INTERNAL.SVCD.DVDrip.DUBBED.DIRFIX-USAnime', 'SVCD' ),
			'Title: Full Metal Panic / Group: USAnime / Episode: 01-02 / Flags: DIRFiX, Dubbed, Internal / Source: DVDRip / Format: SVCD / Type: TV'
		],
		[
			'TV #2 - Special season + episode pattern: S2.E07',
			new ReleaseParser( '24.Twenty.Four.S2.E07.German.DVDRiP.Line.Dubbed.SVCD-SOF', 'SVCD' ),
			'Title: 24 Twenty Four / Group: SOF / Season: 2 / Episode: 7 / Flags: Line dubbed / Source: DVDRip / Format: SVCD / Language: German / Type: TV'
		],
		[
			'TV #3 - Lots of numbers in title',
			new ReleaseParser( '24.S02E02.9.00.Uhr.bis.10.00.Uhr.German.DL.TV.Dubbed.DVDRip.SVCD.READ.NFO-c0nFuSed', 'TV' ),
			'Show: 24 / Title: 9 00 Uhr bis 10 00 Uhr / Group: c0nFuSed / Season: 2 / Episode: 2 / Flags: READNFO, TV Dubbed / Source: DVDRip / Format: SVCD / Language: German, Multilingual / Type: TV'
		],
		[
			'TV #4',
			new ReleaseParser( 'Direct.Talk.S09E09.Mizutani.Yoshihiro.Relief.Beds.Made.of.Cardboard.1080p.HDTV.H264-DARKFLiX', 'tv' ),
			'Show: Direct Talk / Title: Mizutani Yoshihiro Relief Beds Made of Cardboard / Group: DARKFLiX / Season: 9 / Episode: 9 / Source: HDTV / Format: h264 / Resolution: 1080p / Type: TV'
		],
		[
			'TV #5 - No episode title given',
			new ReleaseParser( 'Dark.Net.S01E06.DOC.SUBFRENCH.720p.WEBRip.x264-TiMELiNE', 'tv' ),
			'Title: Dark Net / Group: TiMELiNE / Season: 1 / Episode: 6 / Flags: Docu, Subbed / Source: WEB / Format: x264 / Resolution: 720p / Type: TV'
		],
		[
			'TV #6 - Old season + episode pattern: 2x14',
			new ReleaseParser( 'The.X-Files.2x14.Die.Hand.Die.Verletzt.DVDRip.XviD.MultiDub-VeLVeT', 'tv' ),
			'Show: The X-Files / Title: Die Hand Die Verletzt / Group: VeLVeT / Season: 2 / Episode: 14 / Flags: Dubbed / Source: DVDRip / Format: XViD / Type: TV'
		],
		[
			'TV #7 - Whole season without episode',
			new ReleaseParser( 'Riverdale.US.S05.PROPER.FRENCH.WEB.x264-STRINGERBELL', 'tv' ),
			'Title: Riverdale / Group: STRINGERBELL / Season: 5 / Flags: Proper / Source: WEB / Format: x264 / Language: French / Country: US / Type: TV'
		],
		[
			'TV #8 - Episode is 0 (needs dirfix but works)',
			new ReleaseParser( '72.Cutest.Animals.S01E0.German.DL.Doku.1080p.WEB.x264-BiGiNT', 'tv' ),
			'Title: 72 Cutest Animals / Group: BiGiNT / Season: 1 / Episode: 0 / Flags: Docu / Source: WEB / Format: x264 / Resolution: 1080p / Language: German, Multilingual / Type: TV'
		],
		[
			'TV #9 - P2P with multi Audio and no extra title',
			new ReleaseParser( 'Gilmore.Girls.S05E01.720p.WEB-DL.AAC2.0.H.264-tK', 'tv' ),
			'Title: Gilmore Girls / Group: tK / Season: 5 / Episode: 1 / Source: WEB / Format: h264 / Resolution: 720p / Audio: AAC, 2.0 / Type: TV'
		],
		[
			'TV #10 - Year before Season + Episode',
			new ReleaseParser( 'Halo.2022.S01E06.POLISH.720p.WEB.H264-A4O', 'tv' ),
			'Title: Halo / Group: A4O / Year: 2022 / Season: 1 / Episode: 6 / Source: WEB / Format: h264 / Resolution: 720p / Language: Polish / Type: TV'
		],
		[
			'TV #11 - Multiple lang in wrong format',
			new ReleaseParser( 'New.Amsterdam.2018.S02E13.In.the.Graveyard.1080p.AMZN.Webrip.x265.10bit.EAC3.5.1.JBENTTAoE', 'tv' ),
			'Show: New Amsterdam / Title: In the Graveyard / Group: NOGRP / Year: 2018 / Season: 2 / Episode: 13 / Source: Amazon / Format: x265 / Resolution: 1080p / Audio: 10BIT, EAC3, 5.1 / Type: TV'
		],
		[
			'TV #12 - rls with comma',
			new ReleaseParser( 'New.Amsterdam.2018.S02E12.14.Years,.2.Months,.8.Days.1080p.AMZN.Webrip.x265.10bit.EAC3.5.1.JBENTTAoE', 'tv' ),
			'Show: New Amsterdam / Title: 14 Years 2 Months 8 Days / Group: NOGRP / Year: 2018 / Season: 2 / Episode: 12 / Source: Amazon / Format: x265 / Resolution: 1080p / Audio: 10BIT, EAC3, 5.1 / Type: TV'
		],
		[
			'TV #13 - Complete Disc instead of episode',
			new ReleaseParser( 'Tulsa.King.S01D03.German.ML.COMPLETE.PAL.DVD9-NAiB', 'DVDR' ),
			'Title: Tulsa King / Group: NAiB / Season: 1 / Disc: 3 / Flags: Complete / Format: DVDR / Resolution: PAL / Language: German, Multilingual / Type: TV'
		],
		[
			'TV #14 - Episode pattern at beginning of release name',
			new ReleaseParser( '4x4.Ule.ja.Umber.Autoga.Colombias.S01E09.EE.1080p.WEB.h264-EMX', 'PRE' ),
			'Title: 4x4 Ule ja Umber Autoga Colombias / Group: EMX / Season: 1 / Episode: 9 / Source: WEB / Format: h264 / Resolution: 1080p / Language: Estonian / Type: TV'
		],
		[
			'TV #15 - Docu with episode',
			new ReleaseParser( 'Speer.Et.Hitler.L.Architecte.Du.Diable.E03.FINAL.DOC.FRENCH.PDTV.XViD-BAWLS', 'tv-xvid' ),
			'Title: Speer Et Hitler L Architecte Du Diable / Group: BAWLS / Episode: 3 / Flags: Docu, Final / Source: PDTV / Format: XViD / Language: French / Type: TV'
		],
		[
			'TV #16 - French season and flags',
			new ReleaseParser( 'Friends.Saison5.Episode5-8.vo.subtitlefrench.DVDRIP.DivX-RoToTo', 'NDS' ),
			'Title: Friends / Group: RoToTo / Season: 5 / Episode: 5-8 / Flags: Subbed / Source: DVDRip / Format: DiVX / Type: TV'
		],

		// TV Sports
		[
			'TV Sports #1',
			new ReleaseParser( 'NFL.2021.09.26.49ers.Vs.Packers.1080p.WEB.h264-SPORTSNET', 'tv' ),
			'Name: NFL / Title: 49ers Vs. Packers / Group: SPORTSNET / Year: 2021 / Date: 26.09.2021 / Source: WEB / Format: h264 / Resolution: 1080p / Type: Sports'
		],
		[
			'TV Sports #2',
			new ReleaseParser( 'Formula1.2021.Russian.Grand.Prix.Highlights.1080p.HDTV.H264-DARKSPORT', 'tv' ),
			'Name: Formula1 / Title: Russian Grand Prix Highlights / Group: DARKSPORT / Year: 2021 / Source: HDTV / Format: h264 / Resolution: 1080p / Type: Sports'
		],
		[
			'TV Sports #3',
			new ReleaseParser( 'WWE.Friday.Night.Smackdown.2021-09-10.German.HDTVRiP.x264-SPORTY', 'tv' ),
			'Name: WWE Friday Night Smackdown / Group: SPORTY / Year: 2021 / Date: 10.09.2021 / Source: HDTV / Format: x264 / Language: German / Type: Sports'
		],
		[
			'TV Sports #4 - Only year and no TV source',
			new ReleaseParser( 'Formula1.2023.Hungarian.Grand.Prix.Practice.Two.1080p.WEB.h264-VERUM', 'X264' ),
			'Name: Formula1 / Title: Hungarian Grand Prix Practice Two / Group: VERUM / Year: 2023 / Source: WEB / Format: h264 / Resolution: 1080p / Type: Sports'
		],
		[
			'TV Sports #5 - False NEW flag',
			new ReleaseParser( 'MLB.2023.08.09.New.York.Yankees.vs.Chicago.White.Sox.720p.WEB.h264-SPORTSNET', 'TV-X264' ),
			'Name: MLB / Title: New York Yankees vs. Chicago White Sox / Group: SPORTSNET / Year: 2023 / Date: 09.08.2023 / Source: WEB / Format: h264 / Resolution: 720p / Type: Sports'
		],
		[
			'TV Sports #6 - DTM',
			new ReleaseParser( 'DTM.2019.05.19.Zolder.2.Rennen.GERMAN.HDTVRiP.x264-SKYHD', 'TV-X264' ),
			'Name: DTM / Title: Zolder 2 Rennen / Group: SKYHD / Year: 2019 / Date: 19.05.2019 / Source: HDTV / Format: x264 / Language: German / Type: Sports'
		],

		// Anime
		[
			'Anime #1',
			new ReleaseParser( 'Bastard.Episode4.Dubbed.OAV.DVDRip.XviD-DVDiSO', 'XVID' ),
			'Title: Bastard / Group: DVDiSO / Episode: 4 / Flags: Dubbed, OVA / Source: DVDRip / Format: XViD / Type: Anime'
		],
		[
			'Anime #2',
			new ReleaseParser( 'Kurokos.Basketball.2nd.Season.NG-shuu.Specials.E06.German.Subbed.2014.ANiME.BDRiP.x264-STARS', 'anime' ),
			'Title: Kurokos Basketball 2nd Season NG-shuu Specials / Group: STARS / Year: 2014 / Episode: 6 / Flags: Anime, Subbed / Source: BDRip / Format: x264 / Language: German / Type: Anime'
		],
		[
			'Anime #3',
			new ReleaseParser( 'Pokemon.23.Der.Film.Geheimnisse.des.Dschungels.German.2020.ANiME.DL.EAC3D.1080p.BluRay.x264-STARS', 'anime' ),
			'Title: Pokemon 23 Der Film Geheimnisse des Dschungels / Group: STARS / Year: 2020 / Flags: Anime / Source: Bluray / Format: x264 / Resolution: 1080p / Audio: EAC3D / Language: German, Multilingual / Type: Anime'
		],
		[
			'Anime #4 - Some rare flags and source',
			new ReleaseParser( 'Romance.Is.In.The.Flash.Of.The.Sword.II.EP01.FANSUBFRENCH.HENTAi.RAWRiP.XViD-Lolicon', 'subs' ),
			'Title: Romance Is In The Flash Of The Sword II / Group: Lolicon / Episode: 1 / Flags: Hentai, Subbed / Source: RAWRiP / Format: XViD / Type: Anime'
		],
		[
			'Anime #5 - Extra title example',
			new ReleaseParser( 'Spy.x.Family.E04.Elterngespraech.an.der.Eliteschule.German.2022.ANiME.DL.BDRiP.x264-STARS', 'ANiME' ),
			'Show: Spy x Family / Title: Elterngespraech an der Eliteschule / Group: STARS / Year: 2022 / Episode: 4 / Flags: Anime / Source: BDRip / Format: x264 / Language: German, Multilingual / Type: Anime'
		],
	

		// Music
		[
			'Music #1',
			new ReleaseParser( 'The.Dells.vs.The.Dramatics.LP-(1974)-diss', 'MP3' ),
			'Title: The Dells vs. The Dramatics LP / Group: diss / Year: 1974 / Type: Music'
		],
		[
			'Music #2',
			new ReleaseParser( 'Zenhiser.Prophet.5.FX.WAV-SONiTUS', '0day' ),
			'Title: Zenhiser Prophet 5 FX / Group: SONiTUS / Format: WAV / Type: Music'
		],
		[
			'Music #3',
			new ReleaseParser( 'Victoria_feat._Sledge-Wanna_Be_(More_Than_Your_Lover)-(DRR-20-1_CD-M)-CDM-FLAC-1998-WRE', 'mp3' ),
			'Artist: Victoria feat. Sledge / Title: Wanna Be (More Than Your Lover) / Group: WRE / Year: 1998 / Source: Maxi CD / Format: FLAC / Type: Music'
		],
		[
			'Music #4 - Web single',
			new ReleaseParser( 'Meschino_-_Romeo-SINGLE-WEB-IT-2023-UOVA', 'MP3' ),
			'Artist: Meschino / Song: Romeo / Group: UOVA / Year: 2023 / Source: Web Single / Language: Italian / Type: Music'
		],
		[
			'Music #5 - Title with brackets and all lowercase',
			new ReleaseParser( '(eiffel_65)-blue_cd_beryl-bpm', 'mp3' ),
			'Artist: (eiffel 65) / Title: blue / Group: bpm / Source: CD / Type: Music'
		],
		[
			'Music #6 - More complex title',
			new ReleaseParser( 'Velarde_x_Luque_x_Vitti_feat_Giovanna_-_Serious_Emotions_2k21_(2nd_Remixes_Pack)-(GR670)-WEB-2021-ZzZz', 'mp3' ),
			'Artist: Velarde x Luque x Vitti feat. Giovanna / Title: Serious Emotions 2k21 (2nd Remixes Pack) / Group: ZzZz / Year: 2021 / Source: WEB / Type: Music'
		],
		[
			'Music #7 - More complex title',
			new ReleaseParser( 'Metallica-the_memory_remains_2-1997-ccmp3d', 'mp3'),
			'Artist: Metallica / Title: the memory remains 2 / Group: ccmp3d / Year: 1997 / Type: Music'
		],
		[
			'Music #8 - Special date parsing',
			new ReleaseParser( 'Statik-Drum_and_Bass_Classic_Mix-9th_January_2001-sour', 'mp3' ),
			'Artist: Statik / Title: Drum and Bass Classic Mix / Group: sour / Year: 2001 / Date: 09.01.2001 / Type: Music'
		],
		[
			'Music #9 - Music with venue and date',
			new ReleaseParser( 'Kraftwerk_-_Live_in_Melbourne_(Australia_29.01.2003)-2CD-Bootleg-2003-BFHMP3', 'mp3' ),
			'Artist: Kraftwerk / Title: Live in Melbourne (Australia 29 01 2003) / Group: BFHMP3 / Year: 2003 / Date: 29.01.2003 / Source: Bootleg / Type: Music'
		],
		[
			'Music #10 - Dont parse source from extra title',
			new ReleaseParser( 'Gilles_Peterson--BBC_Radio_6_Music-DVBS-08-05-2023-OMA', 'mp3' ),
			'Artist: Gilles Peterson / Title: BBC Radio 6 Music / Group: OMA / Year: 2023 / Date: 08.05.2023 / Source: DVB / Type: Music'
		],
		[
			'Music #11 - Dont parse flag from extra title',
			new ReleaseParser( 'Vicetone_-_The_World_Has_A_Heartbeat_(Incl._Extended_Mix)-(30022913)-WEB-2023-JUSTiFY_iNT', 'mp3' ),
			'Artist: Vicetone / Title: The World Has A Heartbeat (Incl. Extended Mix) / Group: JUSTiFY_iNT / Year: 2023 / Source: WEB / Type: Music'
		],
		[
			'Music #12 - Dont parse source from extra title',
			new ReleaseParser( 'Victoria_feat._Sledge-Wanna_Be_(More_Than_Your_Lover)-(DRR-20-1_CD-M)-CDM-FLAC-1998-WRE', 'mp3' ),
			'Artist: Victoria feat. Sledge / Title: Wanna Be (More Than Your Lover) / Group: WRE / Year: 1998 / Source: Maxi CD / Format: FLAC / Type: Music'
		],
		[
			'Music #13 - Try to exclude catalogue stuff',
			new ReleaseParser( 'VA-The_Collapse_Of_Future_Vol_10_Part_1-TCOF10P1-WEB-2023-WAV', 'mp3' ),
			'Artist: Various / Title: The Collapse Of Future Vol. 10 Part 1 / Group: WAV / Year: 2023 / Source: WEB / Type: Music'
		],

		// EBook
		[
			'eBook #1 - Basic with title and title extra',
			new ReleaseParser( 'IDW.-.Witch.And.Wizard.Battle.For.Shadowland.2012.Hybrid.Comic.eBook-BitBook', 'ebook' ),
			'Author: IDW / Title: Witch And Wizard Battle For Shadowland / Group: BitBook / Year: 2012 / Flags: Comic / Format: Hybrid / Type: eBook'
		],
		[
			'eBook #2 - Newspaper with date (09.28.2021) = 28.09.2021 (reformatted)',
			new ReleaseParser( 'La.Gazzetta.Dello.Sport.09.28.2021.iTALiAN.RETAiL.eBook-DiVER', 'ebook' ),
			'Title: La Gazzetta Dello Sport / Group: DiVER / Year: 2021 / Date: 28.09.2021 / Flags: Retail / Language: Italian / Type: eBook'
		],
		[
			'eBook #3 - Magazine with monthname and year (September 20201)',
			new ReleaseParser( 'Scootering.September.2021.HYBRiD.MAGAZiNE.eBook-PAPERCLiPS', 'ebook' ),
			'Title: Scootering / Group: PAPERCLiPS / Year: 2021 / Date: 01.09.2021 / Flags: Magazine / Format: Hybrid / Type: eBook'
		],
		[
			'eBook #4 - Comic with title, title_extra and issue number (No.04)',
			new ReleaseParser( 'Viper.Comics.-.Ichabod.Jones.Monster.Hunter.No.04.2012.Hybrid.Comic.eBook-BitBook', 'ebook' ),
			'Author: Viper Comics / Title: Ichabod Jones Monster Hunter / Group: BitBook / Year: 2012 / Issue: 4 / Flags: Comic / Format: Hybrid / Type: eBook'
		],
		[
			'eBook #5 - Basic magazine with issue number (No.314)',
			new ReleaseParser( 'EDGE.No.314.2018.HYBRiD.MAGAZiNE.eBook-PAPERCLiPS', 'ebook' ),
			'Title: EDGE / Group: PAPERCLiPS / Year: 2018 / Issue: 314 / Flags: Magazine / Format: Hybrid / Type: eBook'
		],
		[
			'eBook #6 - Other version of issue number (N119)',
			new ReleaseParser( 'Prog.N119.2021.RETAiL.MAGAZiNE.eBook-PRiNTER', 'ebook' ),
			'Title: Prog / Group: PRiNTER / Year: 2021 / Issue: 119 / Flags: Magazine, Retail / Type: eBook'
		],
		[
			'eBook #7 - Other version of issue number (Band)',
			new ReleaseParser( 'Die.Enwor.Saga.Band.05.-.Das.Schwarze.Schiff.German.Ebook-Elements', 'ebook' ),
			'Author: Die Enwor Saga / Title: Das Schwarze Schiff / Group: Elements / Issue: 5 / Language: German / Type: eBook'
		],
		[
			'eBook #8 - Other version of issue number (Issue)',
			new ReleaseParser( 'The.Amazing.Spiderman.Issue.501.January.2004.Comic.eBook-Dementia', 'ebook' ),
			'Title: The Amazing Spiderman / Group: Dementia / Year: 2004 / Date: 01.01.2004 / Issue: 501 / Flags: Comic / Type: eBook'
		],
		[
			'eBook #9 - Other version of issue number',
			new ReleaseParser( 'Simpsons.Comics.Ausgabe.15.Januar.1998.German.Comic.eBook-HS', 'ebook' ),
			'Title: Simpsons Comics / Group: HS / Year: 1998 / Date: 01.01.1998 / Issue: 15 / Flags: Comic / Language: German / Type: eBook'
		],
		[
			'eBook #10 - title + title_extra + special date formatting (15 Januar 2004)',
			new ReleaseParser( 'Concorde.-.Die.Traumer.15.Januar.2004.Presseheft.German.Ebook-Elements', 'ebook' ),
			'Author: Concorde / Title: Die Traumer / Group: Elements / Year: 2004 / Date: 15.01.2004 / Language: German / Type: eBook'
		],
		[
			'eBook #11 - Basic book with author and book title',
			new ReleaseParser( 'Gerd.Postel.-.Gestaendnisse.Eines.Falschen.Doktors.German.Ebook-Elements', 'ebook' ),
			'Author: Gerd Postel / Title: Gestaendnisse Eines Falschen Doktors / Group: Elements / Language: German / Type: eBook'
		],
		[
			'eBook #12 - Issue is 0',
			new ReleaseParser( 'IDW.-.Machete.No.0.2010.Hybrid.Comic.eBook-BitBook', 'ebook' ),
			'Author: IDW / Title: Machete / Group: BitBook / Year: 2010 / Issue: 0 / Flags: Comic / Format: Hybrid / Type: eBook'
		],

		// Abook
		[
			'Abook #1 - Title + title_extra + episode number (F04)',
			new ReleaseParser( 'Otfried_Preussler_-_Der_Hotzenplotz_Geht_Um-F04-(Audiobook)-DE-2001-S8', 'Abook' ),
			'Author: Otfried Preussler / Title: Der Hotzenplotz Geht Um / Group: S8 / Year: 2001 / Episode: 4 / Language: German / Type: ABook'
		],
		[
			'Abook #2 - Simple named abook',
			new ReleaseParser( 'York-2CD-ABOOK-DE-2001-sUppLeX', 'abook' ),
			'Title: York / Group: sUppLeX / Year: 2001 / Source: CD / Language: German / Type: ABook'
		],
		[
			'Abook #3 - Other version of episode number (Folge 212)',
			new ReleaseParser( 'Die_Drei_Fragezeichen--Folge_212_und_der_weisse_Leopard-AUDIOBOOK-WEB-DE-2021-OMA', 'abook' ),
			'Author: Die Drei Fragezeichen / Title: und der weisse Leopard / Group: OMA / Year: 2021 / Episode: 212 / Source: WEB / Language: German / Type: ABook'
		],
		[
			'Abook #4',
			new ReleaseParser( 'Perry_Rhodan-SE_117_Duell_Der_Erbfeinde-ABOOK-DE-MP3CD-2021-FLUiD', 'abook' ),
			'Author: Perry Rhodan / Title: Duell Der Erbfeinde / Group: FLUiD / Year: 2021 / Episode: 117 / Source: MP3 CD / Language: German / Type: ABook'
		],
		[
			'Abook #5',
			new ReleaseParser( 'Jan_Tenner_Der_Neue_Superheld-F20_Rueckkehr_Ins_Reich_Der_Azzarus-Audiobook-DE-2021-VOiCE', 'abook' ),
			'Author: Jan Tenner Der Neue Superheld / Title: Rueckkehr Ins Reich Der Azzarus / Group: VOiCE / Year: 2021 / Episode: 20 / Language: German / Type: ABook'
		],

		// XXX
		[
			'XXX #1 - Website/Publisher + Date + Title',
			new ReleaseParser( 'NaughtyAmerica.com_17.10.12.Sloan.Harper.Sean.Lawless.Dirty.Wives.Club.XXX.IMAGESET-FuGLi', 'imgset' ),
			'Publisher: NaughtyAmerica.com / Title: Sloan Harper Sean Lawless Dirty Wives Club / Group: FuGLi / Year: 2017 / Date: 12.10.2017 / Flags: Imageset, XXX / Type: XXX'
		],
		[
			'XXX #2 - XXX with episode and special flags',
			new ReleaseParser( 'Lustery.E727.Leo.And.Madly.A.Happy.Ending.For.Him.XXX.VERTICAL.HRp.MP4-WRB', 'XXX' ),
			'Publisher: Lustery / Title: Leo And Madly A Happy Ending For Him / Group: WRB / Episode: 727 / Flags: HR, Vertical, XXX / Format: MP4 / Type: XXX'
		],

		

		// MusicVideo
		[
			'MusicVideo #1 - Recorded on a live show with special date (like normal music live recording)',
			new ReleaseParser( 'John_Mayer-Wild_Blue_(The_Late_Show_2021-09-23)-DDC-1080p-x264-2021-SRPx', 'mvid' ),
			'Artist: John Mayer / Title: Wild Blue (The Late Show 2021-09-23) / Group: SRPx / Year: 2021 / Date: 23.09.2021 / Source: DDC / Format: x264 / Resolution: 1080p / Type: MusicVideo'
		],
		[
			'MusicVideo #2 - Same as above, other date formatting',
			new ReleaseParser( 'David_Guetta_ft_Nicki_Minaj_and_FloRida-Where_Them_Girls_At_(Americas_Got_Talent_08-31-11)-HDTV-720p-X264-2011-2LC', 'mvid' ),
			'Artist: David Guetta ft. Nicki Minaj and FloRida / Title: Where Them Girls At (Americas Got Talent 08-31-11) / Group: 2LC / Year: 2011 / Date: 31.08.2011 / Source: HDTV / Format: x264 / Resolution: 720p / Type: MusicVideo'
		],
		[
			'MusicVideo #3 - Not a TV Show, Dont parse DAT as source',
			new ReleaseParser( 'T-Pain_ft_Kehlani-I_Like_Dat_(Jimmy_Kimmel_Live_2021-06-09)-DDC-720p-x264-2021-SRPx', 'tv-hd-x264' ),
			'Artist: T / Title: Pain ft. Kehlani-I Like Dat (Jimmy Kimmel Live 2021-06-09) / Group: SRPx / Year: 2021 / Date: 09.06.2021 / Source: DDC / Format: x264 / Resolution: 720p / Type: MusicVideo'
		],
	];

	$i = 1;
	$passed = 0;
	$failed = 0;

	// Loop all test scenarios
	foreach( $tests as $test )
	{
		if ( $test[1] == $test[2] )
		{
			$output = !empty( $test[1]->get( 'title_extra' ) ) ? $test[0] . " / " . $test[1]->get( 'title' ) . " $white-$reset " . $test[1]->get( 'title_extra' ) : $test[0] . " / " . $test[1]->get( 'title' );
			echo "$green($i)$bold$greenlight ✓ Passed:$reset $output" . \PHP_EOL;
			$passed++;
		}
		else
		{
			echo "$redlight($i)$bold$red ✘ Failed:$reset " . $test[1]->get( 'release' ) . "\n$greenlightbg$black Right > $reset $test[2]\n$redbg$black Wrong > $reset $test[0]\n";
			$failed++;
		}

		$i++;
		// Let tests run slower, so we can actually see if one test fails.
		// Normal test speed without usleep is about 100ms
		\usleep(15000);
	}

	echo \PHP_EOL . 'All tests finished in ' . \round( \microtime( \true ) - $start_time, 4 ) . 's' . \PHP_EOL . \PHP_EOL;

	echo "$bold$greenlight ✓ Passed:$reset $passed" . \PHP_EOL;
	echo "$bold$red ✘ Failed:$reset $failed" . \PHP_EOL;
}


/**
 * Single test for debugging.
 * 
 * @test
 */

function release_parser_test_single()
{
	echo \PHP_EOL . 'Starting ReleaseParser Single test ...' . \PHP_EOL . \PHP_EOL;
	$release_name = 'Pay.the.Ghost.2015.1080p.HULU.WEB-DL.DDP.5.1.H.264-PiRaTeS[TGx]';
	$release_section = '';
	$release = new ReleaseParser( $release_name, $release_section );

	// Check if expectation matches parsed.
	echo '[Original] ' . $release_name . \PHP_EOL;
	echo '  [Parsed] ' . $release . \PHP_EOL;

	\print_r( $release );
}


/**
 * Read release names fomr file for faster checking.
 * Filename: releases.txt
 * 
 * @test
 */

function release_parser_test_file()
{
	$handle = \fopen( __DIR__ . '/releases.txt', 'r');

	if ( $handle )
	{
		$counter = 1;
		while ( ($line = \fgets( $handle ) ) !== \false)
		{
			echo $counter . ' > ' . $line;
			echo $counter . ' > ' . new ReleaseParser( $line ) . \PHP_EOL . \PHP_EOL;
			$counter++;
		}

		fclose( $handle );
	}
	else
	{
		echo 'Failed to open the file';
	}
}

// Do tests.
release_parser_test();
release_parser_test_single();
//release_parser_test_file();