<?php
/**
 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />
 * File:        securimage.php<br />
 *
 * Copyright (c) 2012, Drew Phillips
 * All rights reserved.
 *
 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA
 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version
 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation
 * @copyright 2012 Drew Phillips
 * @author Drew Phillips <drew@drew-phillips.com>
 * @version 3.2RC3 (May 2012)
 * @package Securimage
 * @license BSD License LICENSE.txt
 */
/**
 * Added showClickMeImage(), jsonCheck() by Douglas <support@idealextensions.com>
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Securimage CAPTCHA Class.
 *
 * @version    3.0
 * @package    Securimage
 * @subpackage classes
 * @author     Drew Phillips <drew@drew-phillips.com>
 *
 */
class Securimage
{
    // All of the public variables below are securimage options
    // They can be passed as an array to the Securimage constructor, set below,
    // or set from securimage_show.php and securimage_play.php

    /**
     * Renders captcha as a JPEG image
     * @var int
     */
    const SI_IMAGE_JPEG = 1;
    /**
     * Renders captcha as a PNG image (default)
     * @var int
     */
    const SI_IMAGE_PNG  = 2;
    /**
     * Renders captcha as a GIF image
     * @var int
     */
    const SI_IMAGE_GIF  = 3;

    /**
     * Create a normal alphanumeric captcha
     * @var int
     */
    const SI_CAPTCHA_STRING     = 0;
    /**
     * Create a captcha consisting of a simple math problem
     * @var int
     */
    const SI_CAPTCHA_MATHEMATIC = 1;

    /**
     * The width of the captcha image
     * @var int
     */
    public $image_width = 215;
    /**
     * The height of the captcha image
     * @var int
     */
    public $image_height = 80;
    /**
     * The type of the image, default = png
     * @var int
     */
    public $image_type   = self::SI_IMAGE_PNG;

    /**
     * The background color of the captcha
     * @var Securimage_Color
     */
    public $image_bg_color = '#ffffff';
    /**
     * The color of the captcha text
     * @var Securimage_Color
     */
    public $text_color     = '#707070';
    /**
     * The color of the lines over the captcha
     * @var Securimage_Color
     */
    public $line_color     = '#707070';
    /**
     * The color of the noise that is drawn
     * @var Securimage_Color
     */
    public $noise_color    = '#707070';

    /**
     * How transparent to make the text 0 = completely opaque, 100 = invisible
     * @var int
     */
    public $text_transparency_percentage = 50;
    /**
     * Whether or not to draw the text transparently, true = use transparency, false = no transparency
     * @var bool
     */
    public $use_transparent_text         = false;

    /**
     * The length of the captcha code
     * @var int
     */
    public $code_length    = 6;
    /**
     * Whether the captcha should be case sensitive (not recommended, use only for maximum protection)
     * @var bool
     */
    public $case_sensitive = false;
    /**
     * The character set to use for generating the captcha code
     * @var string
     */
    public $charset        = 'ABCDEFGHKLMNPRSTUVWYZabcdefghklmnprstuvwyz23456789';
    /**
     * How long in seconds a captcha remains valid, after this time it will not be accepted
     * @var unknown_type
     */
    public $expiry_time    = 900;

    /**
     * The session name securimage should use, only set this if your application uses a custom session name
     * It is recommended to set this value below so it is used by all securimage scripts
     * @var string
     */
    public $session_name   = null;

    /**
     * true to use the wordlist file, false to generate random captcha codes
     * @var bool
     */
    public $use_wordlist   = false;

    /**
     * The level of distortion, 0.75 = normal, 1.0 = very high distortion
     * @var double
     */
    public $perturbation = 0.75;
    /**
     * How many lines to draw over the captcha code to increase security
     * @var int
     */
    public $num_lines    = 8;
    /**
     * The level of noise (random dots) to place on the image, 0-10
     * @var int
     */
    public $noise_level  = 0;

    /**
     * The signature text to draw on the bottom corner of the image
     * @var string
     */
    public $image_signature = '';
    /**
     * The color of the signature text
     * @var Securimage_Color
     */
    public $signature_color = '#707070';
    /**
     * The path to the ttf font file to use for the signature text, defaults to $ttf_file (AHGBold.ttf)
     * @var string
     */
    public $signature_font;

    /**
     * Use an SQLite database to store data (for users that do not support cookies)
     * @var bool
     */
    public $use_sqlite_db = false;

    /**
     * The type of captcha to create, either alphanumeric, or a math problem<br />
     * Securimage::SI_CAPTCHA_STRING or Securimage::SI_CAPTCHA_MATHEMATIC
     * @var int
     */
    public $captcha_type  = self::SI_CAPTCHA_STRING; // or self::SI_CAPTCHA_MATHEMATIC;

    /**
     * The captcha namespace, use this if you have multiple forms on a single page, blank if you do not use multiple forms on one page
     * @var string
     * <code>
     * <?php
     * // in securimage_show.php (create one show script for each form)
     * $img->namespace = 'contact_form';
     *
     * // in form validator
     * $img->namespace = 'contact_form';
     * if ($img->check($code) == true) {
     *     echo "Valid!";
     *  }
     * </code>
     */
    public $namespace;

    /**
     * The font file to use to draw the captcha code, leave blank for default font AHGBold.ttf
     * @var string
     */
    public $ttf_file;
    /**
     * The path to the wordlist file to use, leave blank for default words/words.txt
     * @var string
     */
    public $wordlist_file;
    /**
     * The directory to scan for background images, if set a random background will be chosen from this folder
     * @var string
     */
    public $background_directory;
    /**
     * The path to the SQLite database file to use, if $use_sqlite_database = true, should be chmod 666
     * @var string
     */
    public $sqlite_database;
    /**
     * The path to the securimage audio directory, can be set in securimage_play.php
     * @var string
     * <code>
     * $img->audio_path = '/home/yoursite/public_html/securimage/audio/';
     * </code>
     */
    public $audio_path;
    /**
     * The path to the directory containing audio files that will be selected
     * randomly and mixed with the captcha audio.
     *
     * @var string
     */
    public $audio_noise_path;
    /**
     * Whether or not to mix background noise files into captcha audio (true = mix, false = no)
     * Mixing random background audio with noise can help improve security of audio captcha.
     * Default: securimage/audio/noise
     *
     * @since 3.0.3
     * @see Securimage::$audio_noise_path
     * @var bool
     */
    public $audio_use_noise;
    /**
     * The method and threshold (or gain factor) used to normalize the mixing with background noise.
     * See http://www.voegler.eu/pub/audio/ for more information.
     *
     * Valid: <ul>
     *     <li> >= 1 - Normalize by multiplying by the threshold (boost - positive gain). <br />
     *            A value of 1 in effect means no normalization (and results in clipping). </li>
     *     <li> <= -1 - Normalize by dividing by the the absolute value of threshold (attenuate - negative gain). <br />
     *            A factor of 2 (-2) is about 6dB reduction in volume.</li>
     *     <li> [0, 1) - (open inverval - not including 1) - The threshold
     *            above which amplitudes are comressed logarithmically. <br />
     *            e.g. 0.6 to leave amplitudes up to 60% "as is" and compress above. </li>
     *     <li> (-1, 0) - (open inverval - not including -1 and 0) - The threshold
     *            above which amplitudes are comressed linearly. <br />
     *            e.g. -0.6 to leave amplitudes up to 60% "as is" and compress above. </li></ul>
     *
     * Default: 0.6
     *
     * @since 3.0.4
     * @var float
     */
    public $audio_mix_normalization = 0.6;
    /**
     * Whether or not to degrade audio by introducing random noise (improves security of audio captcha)
     * Default: true
     *
     * @since 3.0.3
     * @var bool
     */
    public $degrade_audio;
    /**
     * Minimum delay to insert between captcha audio letters in milliseconds
     *
     * @since 3.0.3
     * @var float
     */
    public $audio_gap_min = 0;
    /**
     * Maximum delay to insert between captcha audio letters in milliseconds
     *
     * @since 3.0.3
     * @var float
     */
    public $audio_gap_max = 600;

    /**
     * Captcha ID if using static captcha
     * @var string Unique captcha id
     */
    protected static $_captchaId = null;

    protected $im;
    protected $tmpimg;
    protected $bgimg;
    protected $iscale = 5;

    protected $securimage_path = null;

    /**
     * The captcha challenge value (either the case-sensitive/insensitive word captcha, or the solution to the math captcha)
     *
     * @var string Captcha challenge value
     */
    protected $code;

    /**
     * The display value of the captcha to draw on the image (the word captcha, or the math equation to present to the user)
     *
     * @var string Captcha display value to draw on the image
     */
    protected $code_display;

    /**
     * A value that can be passed to the constructor that can be used to generate a captcha image with a given value
     * This value does not get stored in the session or database and is only used when calling Securimage::show().
     * If a display_value was passed to the constructor and the captcha image is generated, the display_value will be used
     * as the string to draw on the captcha image.  Used only if captcha codes are generated and managed by a 3rd party app/library
     *
     * @var string Captcha code value to display on the image
     */
    protected $display_value;

    /**
     * Captcha code supplied by user [set from Securimage::check()]
     *
     * @var string
     */
    protected $captcha_code;

    /**
     * Flag that can be specified telling securimage not to call exit after generating a captcha image or audio file
     *
     * @var bool If true, script will not terminate; if false script will terminate (default)
     */
    protected $no_exit;

    /**
     * Flag indicating whether or not a PHP session should be started and used
     *
     * @var bool If true, no session will be started; if false, session will be started and used to store data (default)
     */
    protected $no_session;

    /**
     * Flag indicating whether or not HTTP headers will be sent when outputting captcha image/audio
     *
     * @var bool If true (default) headers will be sent, if false, no headers are sent
     */
    protected $send_headers;

    // sqlite database handle (if using sqlite)
    protected $sqlite_handle;

    // gd color resources that are allocated for drawing the image
    protected $gdbgcolor;
    protected $gdtextcolor;
    protected $gdlinecolor;
    protected $gdsignaturecolor;

    /**
     * Create a new securimage object, pass options to set in the constructor.<br />
     * This can be used to display a captcha, play an audible captcha, or validate an entry
     * @param array $options
     * <code>
     * $options = array(
     *     'text_color' => new Securimage_Color('#013020'),
     *     'code_length' => 5,
     *     'num_lines' => 5,
     *     'noise_level' => 3,
     *     'font_file' => Securimage::getPath() . '/custom.ttf'
     * );
     *
     * $img = new Securimage($options);
     * </code>
     */
    public function __construct($options = array())
    {
        $this->securimage_path = dirname(__FILE__);

        if (is_array($options) && sizeof($options) > 0) {
            foreach($options as $prop => $val) {
                if ($prop == 'captchaId') {
                    Securimage::$_captchaId = $val;
                    $this->use_sqlite_db    = true;
                } else {
                    $this->$prop = $val;
                }
            }
        }

        $this->image_bg_color  = $this->initColor($this->image_bg_color,  '#ffffff');
        $this->text_color      = $this->initColor($this->text_color,      '#616161');
        $this->line_color      = $this->initColor($this->line_color,      '#616161');
        $this->noise_color     = $this->initColor($this->noise_color,     '#616161');
        $this->signature_color = $this->initColor($this->signature_color, '#616161');

        if (is_null($this->ttf_file)) {
            $this->ttf_file = $this->securimage_path . '/AHGBold.ttf';
        }

        $this->signature_font = $this->ttf_file;

        if (is_null($this->wordlist_file)) {
            $this->wordlist_file = $this->securimage_path . '/words/words.txt';
        }

        if (is_null($this->sqlite_database)) {
            $this->sqlite_database = $this->securimage_path . '/database/securimage.sqlite';
        }

        if (is_null($this->audio_path)) {
            $this->audio_path = $this->securimage_path . '/audio/';
        }

        if (is_null($this->audio_noise_path)) {
            $this->audio_noise_path = $this->audio_path . '/noise/';
        }

        if (is_null($this->audio_use_noise)) {
            $this->audio_use_noise = true;
        }

        if (is_null($this->degrade_audio)) {
            $this->degrade_audio = true;
        }

        if (is_null($this->code_length) || (int)$this->code_length < 1) {
            $this->code_length = 6;
        }

        if (is_null($this->perturbation) || !is_numeric($this->perturbation)) {
            $this->perturbation = 0.75;
        }

        if (is_null($this->namespace) || !is_string($this->namespace)) {
            $this->namespace = 'default';
        }

        if (is_null($this->no_exit)) {
            $this->no_exit = false;
        }

        if (is_null($this->no_session)) {
            $this->no_session = false;
        }

        if (is_null($this->send_headers)) {
            $this->send_headers = true;
        }

        if ($this->no_session != true) {
            // Initialize session or attach to existing
            if ( session_id() == '' ) { // no session has been started yet, which is needed for validation
                if (!is_null($this->session_name) && trim($this->session_name) != '') {
                    session_name(trim($this->session_name)); // set session name if provided
                }
                session_start();
            }
        }
    }

    /**
     * Return the absolute path to the Securimage directory
     * @return string The path to the securimage base directory
     */
    public static function getPath()
    {
        return dirname(__FILE__);
    }

    /**
     * Generate a new captcha ID or retrieve the current ID
     *
     * @param $new bool If true, generates a new challenge and returns and ID
     * @param $options array Additional options to be passed to Securimage
     *
     * @return null|string Returns null if no captcha id set and new was false, or string captcha ID
     */
    public static function getCaptchaId($new = true, array $options = array())
    {
        if ((bool)$new == true) {
            $id = sha1(uniqid($_SERVER['REMOTE_ADDR'], true));
            $opts = array('no_session'    => true,
                          'use_sqlite_db' => true);
            if (sizeof($options) > 0) $opts = array_merge($opts, $options);
            $si = new self($opts);
            Securimage::$_captchaId = $id;
            $si->createCode();

            return $id;
        } else {
            return Securimage::$_captchaId;
        }
    }

    /**
     * Validate a captcha code input against a captcha ID
     * @param string $id The captcha ID to check
     * @param string $value The captcha value supplied by the user
     *
     * @return bool true if the code was valid for the given captcha ID, false if not or if database failed to open
     */
    public static function checkByCaptchaId($id, $value)
    {
        $si = new self(array('captchaId'     => $id,
                             'no_session'    => true,
                             'use_sqlite_db' => true));

        if ($si->openDatabase()) {
            $code = $si->getCodeFromDatabase();

            if (is_array($code)) {
                $si->code         = $code['code'];
                $si->code_display = $code['code_disp'];
            }

            if ($si->check($value)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Used to serve a captcha image to the browser
     * @param string $background_image The path to the background image to use
     * <code>
     * $img = new Securimage();
     * $img->code_length = 6;
     * $img->num_lines   = 5;
     * $img->noise_level = 5;
     *
     * $img->show(); // sends the image to browser
     * exit;
     * </code>
     */
    public function show($background_image = '')
    {
        set_error_handler(array(&$this, 'errorHandler'));

        if($background_image != '' && is_readable($background_image)) {
            $this->bgimg = $background_image;
        }

        $this->doImage();
    }

    /**
     * Check a submitted code against the stored value
     * @param string $code  The captcha code to check
     * <code>
     * $code = $_POST['code'];
     * $img  = new Securimage();
     * if ($img->check($code) == true) {
     *     $captcha_valid = true;
     * } else {
     *     $captcha_valid = false;
     * }
     * </code>
     */
    public function check($code)
    {
        $this->code_entered = $code;
        $this->validate();
        return $this->correct_code;
    }

    /**
     * Output a wav file of the captcha code to the browser
     *
     * <code>
     * $img = new Securimage();
     * $img->outputAudioFile(); // outputs a wav file to the browser
     * exit;
     * </code>
     */
    public function outputAudioFile()
    {
        set_error_handler(array(&$this, 'errorHandler'));

        require_once dirname(__FILE__) . '/WavFile.php';

        try {
            $audio = $this->getAudibleCode();
        } catch (Exception $ex) {
            if (($fp = @fopen(dirname(__FILE__) . '/si.error_log', 'a+')) !== false) {
                fwrite($fp, date('Y-m-d H:i:s') . ': Securimage audio error "' . $ex->getMessage() . '"' . "\n");
                fclose($fp);
            }

            $audio = $this->audioError();
        }

        if ($this->canSendHeaders() || $this->send_headers == false) {
            if ($this->send_headers) {
                $uniq = md5(uniqid(microtime()));
                header("Content-Disposition: attachment; filename=\"securimage_audio-{$uniq}.wav\"");
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Expires: Sun, 1 Jan 2000 12:00:00 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . 'GMT');
                header('Content-type: audio/x-wav');

                if (extension_loaded('zlib')) {
                    ini_set('zlib.output_compression', true);  // compress output if supported by browser
                } else {
                    header('Content-Length: ' . strlen($audio));
                }
            }

            echo $audio;
        } else {
            echo '<hr /><strong>'
                .'Failed to generate audio file, content has already been '
                .'output.<br />This is most likely due to misconfiguration or '
                .'a PHP error was sent to the browser.</strong>';
        }

        restore_error_handler();

        if (!$this->no_exit) exit;
    }


    /**
     * @param string challenge code entered
     * @return bool
     * @author Douglas Machado
     */
    public function jsonCheck($code_entered) {
    	$code = $this->getCode();
    	// returns stored code, or an empty string if no stored code was found
    
    	if ($this->case_sensitive == false && preg_match('/[A-Z]/', $code)) {
    		// case sensitive was set from securimage_show.php but not in class
    		// the code saved in the session has capitals so set case sensitive to true
    		$this->case_sensitive = true;
    	}
    
    	$code_entered = trim( (($this->case_sensitive) ? $code_entered : strtolower($code_entered)) );
    
    	if ($code != '') {
    		if ($code == $code_entered) {
    			return true;
    		}
    	}
    	return false;
    }
    

    /**
     * D
     */
    public function showClickMeImage($background_image='',$code='Click me')
    {
    	set_error_handler(array(&$this, 'errorHandler'));
    	
    	$this->code_display = $code;
    	$this->code			= $code;
    	
    	if($background_image != '' && is_readable($background_image)) {
    		$this->bgimg = $background_image;
    	}
    	
    	if( ($this->use_transparent_text == true || $this->bgimg != '') && function_exists('imagecreatetruecolor')) {
    		$imagecreate = 'imagecreatetruecolor';
    	} else {
    		$imagecreate = 'imagecreate';
    	}
    
    	$this->im     = $imagecreate($this->image_width, $this->image_height);
    	$this->tmpimg = $imagecreate($this->image_width * $this->iscale, $this->image_height * $this->iscale);
    
    	$this->allocateColors();
    	imagepalettecopy($this->tmpimg, $this->im);
    
    	$this->setBackground();
    	
    	if ($this->num_lines > 0) {
    		//s$this->drawLines();
    	}
    	
    	
    	$bbox = imagettfbbox(15, 0, $this->signature_font, $code);
        $textlen = $bbox[2] - $bbox[0];
        $x = $this->image_width - ($textlen *1.2);
        if($x < 0){$x = 0;}
        $y = $this->image_height - 30;
        $font_size = $this->image_width/34 + 1000/$textlen;
       
        imagettftext($this->im, $font_size, 0, $x, $y, $this->gdtextcolor, $this->signature_font, $code);
    
       
        
        
    	$this->output();
    }
    
    
    /**
     * Return the code from the session or sqlite database if used.  If none exists yet, an empty string is returned
     *
     * @param $array bool   True to receive an array containing the code and properties
     * @return array|string Array if $array = true, otherwise a string containing the code
     */
    public function getCode($array = false)
    {
        $code = '';
        $time = 0;
        $disp = 'error';

        if ($this->no_session != true) {
            if (isset($_SESSION['securimage_code_value'][$this->namespace]) &&
                    trim($_SESSION['securimage_code_value'][$this->namespace]) != '') {
                if ($this->isCodeExpired(
                        $_SESSION['securimage_code_ctime'][$this->namespace]) == false) {
                    $code = $_SESSION['securimage_code_value'][$this->namespace];
                    $time = $_SESSION['securimage_code_ctime'][$this->namespace];
                    $disp = $_SESSION['securimage_code_disp'] [$this->namespace];
                }
            }
        }

        if ($this->use_sqlite_db == true && function_exists('sqlite_open')) {
            // no code in session - may mean user has cookies turned off
            $this->openDatabase();
            $code = $this->getCodeFromDatabase();
        } else { /* no code stored in session or sqlite database, validation will fail */ }

        if ($array == true) {
            return array('code' => $code, 'ctime' => $time, 'display' => $disp);
        } else {
            return $code;
        }
    }

    /**
     * The main image drawing routing, responsible for constructing the entire image and serving it
     */
    protected function doImage()
    {
        if( ($this->use_transparent_text == true || $this->bgimg != '') && function_exists('imagecreatetruecolor')) {
            $imagecreate = 'imagecreatetruecolor';
        } else {
            $imagecreate = 'imagecreate';
        }

        $this->im     = $imagecreate($this->image_width, $this->image_height);
        $this->tmpimg = $imagecreate($this->image_width * $this->iscale, $this->image_height * $this->iscale);

        $this->allocateColors();
        imagepalettecopy($this->tmpimg, $this->im);

        $this->setBackground();

        $code = '';

        if ($this->getCaptchaId(false) !== null) {
            // a captcha Id was supplied

            // check to see if a display_value for the captcha image was set
            if (is_string($this->display_value) && strlen($this->display_value) > 0) {
                $this->code_display = $this->display_value;
                $this->code         = ($this->case_sensitive) ?
                                       $this->display_value   :
                                       strtolower($this->display_value);
                $code = $this->code;
            } else if ($this->openDatabase()) {
                // no display_value, check the database for existing captchaId
                $code = $this->getCodeFromDatabase();

                // got back a result from the database with a valid code for captchaId
                if (is_array($code)) {
                    $this->code         = $code['code'];
                    $this->code_display = $code['code_disp'];
                    $code = $code['code'];
                }
            }
        }

        if ($code == '') {
            // if the code was not set using display_value or was not found in
            // the database, create a new code
            $this->createCode();
        }

        if ($this->noise_level > 0) {
            $this->drawNoise();
        }

        $this->drawWord();

        if ($this->perturbation > 0 && is_readable($this->ttf_file)) {
            $this->distortedCopy();
        }

        if ($this->num_lines > 0) {
            $this->drawLines();
        }

        if (trim($this->image_signature) != '') {
            $this->addSignature();
        }

        $this->output();
    }

    /**
     * Allocate the colors to be used for the image
     */
    protected function allocateColors()
    {
        // allocate bg color first for imagecreate
        $this->gdbgcolor = imagecolorallocate($this->im,
                                              $this->image_bg_color->r,
                                              $this->image_bg_color->g,
                                              $this->image_bg_color->b);

        $alpha = intval($this->text_transparency_percentage / 100 * 127);

        if ($this->use_transparent_text == true) {
            $this->gdtextcolor = imagecolorallocatealpha($this->im,
                                                         $this->text_color->r,
                                                         $this->text_color->g,
                                                         $this->text_color->b,
                                                         $alpha);
            $this->gdlinecolor = imagecolorallocatealpha($this->im,
                                                         $this->line_color->r,
                                                         $this->line_color->g,
                                                         $this->line_color->b,
                                                         $alpha);
            $this->gdnoisecolor = imagecolorallocatealpha($this->im,
                                                          $this->noise_color->r,
                                                          $this->noise_color->g,
                                                          $this->noise_color->b,
                                                          $alpha);
        } else {
            $this->gdtextcolor = imagecolorallocate($this->im,
                                                    $this->text_color->r,
                                                    $this->text_color->g,
                                                    $this->text_color->b);
            $this->gdlinecolor = imagecolorallocate($this->im,
                                                    $this->line_color->r,
                                                    $this->line_color->g,
                                                    $this->line_color->b);
            $this->gdnoisecolor = imagecolorallocate($this->im,
                                                          $this->noise_color->r,
                                                          $this->noise_color->g,
                                                          $this->noise_color->b);
        }

        $this->gdsignaturecolor = imagecolorallocate($this->im,
                                                     $this->signature_color->r,
                                                     $this->signature_color->g,
                                                     $this->signature_color->b);

    }

    /**
     * The the background color, or background image to be used
     */
    protected function setBackground()
    {
        // set background color of image by drawing a rectangle since imagecreatetruecolor doesn't set a bg color
        imagefilledrectangle($this->im, 0, 0,
                             $this->image_width, $this->image_height,
                             $this->gdbgcolor);
        imagefilledrectangle($this->tmpimg, 0, 0,
                             $this->image_width * $this->iscale, $this->image_height * $this->iscale,
                             $this->gdbgcolor);

        if ($this->bgimg == '') {
            if ($this->background_directory != null &&
                is_dir($this->background_directory) &&
                is_readable($this->background_directory))
            {
                $img = $this->getBackgroundFromDirectory();
                if ($img != false) {
                    $this->bgimg = $img;
                }
            }
        }

        if ($this->bgimg == '') {
            return;
        }

        $dat = @getimagesize($this->bgimg);
        if($dat == false) {
            return;
        }

        switch($dat[2]) {
            case 1:  $newim = @imagecreatefromgif($this->bgimg); break;
            case 2:  $newim = @imagecreatefromjpeg($this->bgimg); break;
            case 3:  $newim = @imagecreatefrompng($this->bgimg); break;
            default: return;
        }

        if(!$newim) return;

        imagecopyresized($this->im, $newim, 0, 0, 0, 0,
                         $this->image_width, $this->image_height,
                         imagesx($newim), imagesy($newim));
    }

    /**
     * Scan the directory for a background image to use
     */
    protected function getBackgroundFromDirectory()
    {
        $images = array();

        if ( ($dh = opendir($this->background_directory)) !== false) {
            while (($file = readdir($dh)) !== false) {
                if (preg_match('/(jpg|gif|png)$/i', $file)) $images[] = $file;
            }

            closedir($dh);

            if (sizeof($images) > 0) {
                return rtrim($this->background_directory, '/') . '/' . $images[rand(0, sizeof($images)-1)];
            }
        }

        return false;
    }

    /**
     * Generates the code or math problem and saves the value to the session
     */
    protected function createCode()
    {
        $this->code = false;

        switch($this->captcha_type) {
            case self::SI_CAPTCHA_MATHEMATIC:
            {
                do {
                    $signs = array('+', '-', 'x');
                    $left  = rand(1, 10);
                    $right = rand(1, 5);
                    $sign  = $signs[rand(0, 2)];

                    switch($sign) {
                        case 'x': $c = $left * $right; break;
                        case '-': $c = $left - $right; break;
                        default:  $c = $left + $right; break;
                    }
                } while ($c <= 0); // no negative #'s or 0

                $this->code         = $c;
                $this->code_display = "$left $sign $right";
                break;
            }

            default:
            {
                if ($this->use_wordlist && is_readable($this->wordlist_file)) {
                    $this->code = $this->readCodeFromFile();
                }

                if ($this->code == false) {
                    $this->code = $this->generateCode($this->code_length);
                }

                $this->code_display = $this->code;
                $this->code         = ($this->case_sensitive) ? $this->code : strtolower($this->code);
            } // default
        }

        $this->saveData();
    }

    /**
     * Draws the captcha code on the image
     */
    protected function drawWord()
    {
        $width2  = $this->image_width * $this->iscale;
        $height2 = $this->image_height * $this->iscale;

        if (!is_readable($this->ttf_file)) {
            imagestring($this->im, 4, 10, ($this->image_height / 2) - 5, 'Failed to load TTF font file!', $this->gdtextcolor);
        } else {
            if ($this->perturbation > 0) {
                $font_size = $height2 * .4;
                $bb = imageftbbox($font_size, 0, $this->ttf_file, $this->code_display);
                $tx = $bb[4] - $bb[0];
                $ty = $bb[5] - $bb[1];
                $x  = floor($width2 / 2 - $tx / 2 - $bb[0]);
                $y  = round($height2 / 2 - $ty / 2 - $bb[1]);

                imagettftext($this->tmpimg, $font_size, 0, $x, $y, $this->gdtextcolor, $this->ttf_file, $this->code_display);
            } else {
                $font_size = $this->image_height * .4;
                $bb = imageftbbox($font_size, 0, $this->ttf_file, $this->code_display);
                $tx = $bb[4] - $bb[0];
                $ty = $bb[5] - $bb[1];
                $x  = floor($this->image_width / 2 - $tx / 2 - $bb[0]);
                $y  = round($this->image_height / 2 - $ty / 2 - $bb[1]);

                imagettftext($this->im, $font_size, 0, $x, $y, $this->gdtextcolor, $this->ttf_file, $this->code_display);
            }
        }

        // DEBUG
        //$this->im = $this->tmpimg;
        //$this->output();

    }

    /**
     * Copies the captcha image to the final image with distortion applied
     */
    protected function distortedCopy()
    {
        $numpoles = 3; // distortion factor
        // make array of poles AKA attractor points
        for ($i = 0; $i < $numpoles; ++ $i) {
            $px[$i]  = rand($this->image_width  * 0.2, $this->image_width  * 0.8);
            $py[$i]  = rand($this->image_height * 0.2, $this->image_height * 0.8);
            $rad[$i] = rand($this->image_height * 0.2, $this->image_height * 0.8);
            $tmp     = ((- $this->frand()) * 0.15) - .15;
            $amp[$i] = $this->perturbation * $tmp;
        }

        $bgCol = imagecolorat($this->tmpimg, 0, 0);
        $width2 = $this->iscale * $this->image_width;
        $height2 = $this->iscale * $this->image_height;
        imagepalettecopy($this->im, $this->tmpimg); // copy palette to final image so text colors come across
        // loop over $img pixels, take pixels from $tmpimg with distortion field
        for ($ix = 0; $ix < $this->image_width; ++ $ix) {
            for ($iy = 0; $iy < $this->image_height; ++ $iy) {
                $x = $ix;
                $y = $iy;
                for ($i = 0; $i < $numpoles; ++ $i) {
                    $dx = $ix - $px[$i];
                    $dy = $iy - $py[$i];
                    if ($dx == 0 && $dy == 0) {
                        continue;
                    }
                    $r = sqrt($dx * $dx + $dy * $dy);
                    if ($r > $rad[$i]) {
                        continue;
                    }
                    $rscale = $amp[$i] * sin(3.14 * $r / $rad[$i]);
                    $x += $dx * $rscale;
                    $y += $dy * $rscale;
                }
                $c = $bgCol;
                $x *= $this->iscale;
                $y *= $this->iscale;
                if ($x >= 0 && $x < $width2 && $y >= 0 && $y < $height2) {
                    $c = imagecolorat($this->tmpimg, $x, $y);
                }
                if ($c != $bgCol) { // only copy pixels of letters to preserve any background image
                    imagesetpixel($this->im, $ix, $iy, $c);
                }
            }
        }
    }

    /**
     * Draws distorted lines on the image
     */
    protected function drawLines()
    {
        for ($line = 0; $line < $this->num_lines; ++ $line) {
            $x = $this->image_width * (1 + $line) / ($this->num_lines + 1);
            $x += (0.5 - $this->frand()) * $this->image_width / $this->num_lines;
            $y = rand($this->image_height * 0.1, $this->image_height * 0.9);

            $theta = ($this->frand() - 0.5) * M_PI * 0.7;
            $w = $this->image_width;
            $len = rand($w * 0.4, $w * 0.7);
            $lwid = rand(0, 2);

            $k = $this->frand() * 0.6 + 0.2;
            $k = $k * $k * 0.5;
            $phi = $this->frand() * 6.28;
            $step = 0.5;
            $dx = $step * cos($theta);
            $dy = $step * sin($theta);
            $n = $len / $step;
            $amp = 1.5 * $this->frand() / ($k + 5.0 / $len);
            $x0 = $x - 0.5 * $len * cos($theta);
            $y0 = $y - 0.5 * $len * sin($theta);

            $ldx = round(- $dy * $lwid);
            $ldy = round($dx * $lwid);

            for ($i = 0; $i < $n; ++ $i) {
                $x = $x0 + $i * $dx + $amp * $dy * sin($k * $i * $step + $phi);
                $y = $y0 + $i * $dy - $amp * $dx * sin($k * $i * $step + $phi);
                imagefilledrectangle($this->im, $x, $y, $x + $lwid, $y + $lwid, $this->gdlinecolor);
            }
        }
    }

    /**
     * Draws random noise on the image
     */
    protected function drawNoise()
    {
        if ($this->noise_level > 10) {
            $noise_level = 10;
        } else {
            $noise_level = $this->noise_level;
        }

        $t0 = microtime(true);

        $noise_level *= 125; // an arbitrary number that works well on a 1-10 scale

        $points = $this->image_width * $this->image_height * $this->iscale;
        $height = $this->image_height * $this->iscale;
        $width  = $this->image_width * $this->iscale;
        for ($i = 0; $i < $noise_level; ++$i) {
            $x = rand(10, $width);
            $y = rand(10, $height);
            $size = rand(7, 10);
            if ($x - $size <= 0 && $y - $size <= 0) continue; // dont cover 0,0 since it is used by imagedistortedcopy
            imagefilledarc($this->tmpimg, $x, $y, $size, $size, 0, 360, $this->gdnoisecolor, IMG_ARC_PIE);
        }

        $t1 = microtime(true);

        $t = $t1 - $t0;

        /*
        // DEBUG
        imagestring($this->tmpimg, 5, 25, 30, "$t", $this->gdnoisecolor);
        header('content-type: image/png');
        imagepng($this->tmpimg);
        exit;
        */
    }

    /**
    * Print signature text on image
    */
    protected function addSignature()
    {
        $bbox = imagettfbbox(10, 0, $this->signature_font, $this->image_signature);
        $textlen = $bbox[2] - $bbox[0];
        $x = $this->image_width - $textlen - 5;
        $y = $this->image_height - 3;

        imagettftext($this->im, 10, 0, $x, $y, $this->gdsignaturecolor, $this->signature_font, $this->image_signature);
    }

    /**
     * Sends the appropriate image and cache headers and outputs image to the browser
     */
    protected function output()
    {
        if ($this->canSendHeaders() || $this->send_headers == false) {
            if ($this->send_headers) {
                // only send the content-type headers if no headers have been output
                // this will ease debugging on misconfigured servers where warnings
                // may have been output which break the image and prevent easily viewing
                // source to see the error.
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");
            }

            switch ($this->image_type) {
                case self::SI_IMAGE_JPEG:
                    if ($this->send_headers) header("Content-Type: image/jpeg");
                    imagejpeg($this->im, null, 90);
                    break;
                case self::SI_IMAGE_GIF:
                    if ($this->send_headers) header("Content-Type: image/gif");
                    imagegif($this->im);
                    break;
                default:
                    if ($this->send_headers) header("Content-Type: image/png");
                    imagepng($this->im);
                    break;
            }
        } else {
            echo '<hr /><strong>'
                .'Failed to generate captcha image, content has already been '
                .'output.<br />This is most likely due to misconfiguration or '
                .'a PHP error was sent to the browser.</strong>';
        }

        imagedestroy($this->im);
        restore_error_handler();

        if (!$this->no_exit) exit;
    }

    /**
     * Gets the code and returns the binary audio file for the stored captcha code
     *
     * @return The audio representation of the captcha in Wav format
     */
    protected function getAudibleCode()
    {
        $letters = array();
        $code    = $this->getCode(true);

        if ($code['code'] == '') {
            $this->createCode();
            $code = $this->getCode(true);
        }

        if (preg_match('/(\d+) (\+|-|x) (\d+)/i', $code['display'], $eq)) {
            $math = true;

            $left  = $eq[1];
            $sign  = str_replace(array('+', '-', 'x'), array('plus', 'minus', 'times'), $eq[2]);
            $right = $eq[3];

            $letters = array($left, $sign, $right);
        } else {
            $math = false;

            $length = strlen($code['display']);

            for($i = 0; $i < $length; ++$i) {
                $letter    = $code['display']{$i};
                $letters[] = $letter;
            }
        }

        try {
            return $this->generateWAV($letters);
        } catch(Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Gets a captcha code from a wordlist
     */
    protected function readCodeFromFile()
    {
        $fp = @fopen($this->wordlist_file, 'rb');
        if (!$fp) return false;

        $fsize = filesize($this->wordlist_file);
        if ($fsize < 128) return false; // too small of a list to be effective

        fseek($fp, rand(0, $fsize - 64), SEEK_SET); // seek to a random position of file from 0 to filesize-64
        $data = fread($fp, 64); // read a chunk from our random position
        fclose($fp);
        $data = preg_replace("/\r?\n/", "\n", $data);

        $start = @strpos($data, "\n", rand(0, 56)) + 1; // random start position
        $end   = @strpos($data, "\n", $start);          // find end of word

        if ($start === false) {
            return false;
        } else if ($end === false) {
            $end = strlen($data);
        }

        return strtolower(substr($data, $start, $end - $start)); // return a line of the file
    }

    /**
     * Generates a random captcha code from the set character set
     */
    protected function generateCode()
    {
        $code = '';

        for($i = 1, $cslen = strlen($this->charset); $i <= $this->code_length; ++$i) {
            $code .= $this->charset{rand(0, $cslen - 1)};
        }

        //return 'testing';  // debug, set the code to given string

        return $code;
    }

    /**
     * Checks the entered code against the value stored in the session or sqlite database, handles case sensitivity
     * Also clears the stored codes if the code was entered correctly to prevent re-use
     */
    protected function validate()
    {
        if (!is_string($this->code) || strlen($this->code) == 0) {
            $code = $this->getCode();
            // returns stored code, or an empty string if no stored code was found
            // checks the session and sqlite database if enabled
        } else {
            $code = $this->code;
        }

        if ($this->case_sensitive == false && preg_match('/[A-Z]/', $code)) {
            // case sensitive was set from securimage_show.php but not in class
            // the code saved in the session has capitals so set case sensitive to true
            $this->case_sensitive = true;
        }

        $code_entered = trim( (($this->case_sensitive) ? $this->code_entered
                                                       : strtolower($this->code_entered))
                        );
        $this->correct_code = false;

        if ($code != '') {
            if ($code == $code_entered) {
                $this->correct_code = true;
                if ($this->no_session != true) {
                    $_SESSION['securimage_code_value'][$this->namespace] = '';
                    $_SESSION['securimage_code_ctime'][$this->namespace] = '';
                }
                $this->clearCodeFromDatabase();
            }
        }
    }

    /**
     * Save data to session namespace and database if used
     */
    protected function saveData()
    {
        if ($this->no_session != true) {
            if (isset($_SESSION['securimage_code_value']) && is_scalar($_SESSION['securimage_code_value'])) {
                // fix for migration from v2 - v3
                unset($_SESSION['securimage_code_value']);
                unset($_SESSION['securimage_code_ctime']);
            }

            $_SESSION['securimage_code_disp'] [$this->namespace] = $this->code_display;
            $_SESSION['securimage_code_value'][$this->namespace] = $this->code;
            $_SESSION['securimage_code_ctime'][$this->namespace] = time();
        }

        $this->saveCodeToDatabase();
    }

    /**
     * Saves the code to the sqlite database
     */
    protected function saveCodeToDatabase()
    {
        $success = false;

        $this->openDatabase();

        if ($this->use_sqlite_db && $this->sqlite_handle !== false) {
            $id      = $this->getCaptchaId(false);
            $ip      = $_SERVER['REMOTE_ADDR'];

            if (empty($id)) {
                $id = $ip;
            }

            $time      = time();
            $code      = $this->code;
            $code_disp = $this->code_display;

            $query = "INSERT OR REPLACE INTO codes(id, ip, code, code_display,"
                    ."namespace, created) VALUES('$id', '$ip', '$code', "
                    ."'$code_disp', '{$this->namespace}', $time)";

            $success = sqlite_query($this->sqlite_handle, $query);
        }

        return $success !== false;
    }

    /**
     * Open sqlite database
     */
    protected function openDatabase()
    {
        $this->sqlite_handle = false;

        if ($this->use_sqlite_db == true && !function_exists('sqlite_open')) {
            trigger_error('Securimage use_sqlite_db option is enable, but SQLIte is not supported by this PHP installation', E_USER_WARNING);
        }

        if ($this->use_sqlite_db && function_exists('sqlite_open')) {
            if (!file_exists($this->sqlite_database)) {
                $fp = fopen($this->sqlite_database, 'w+');
                if (!$fp) {
                    trigger_error('Securimage failed to open sqlite database "' . $this->sqlite_database, E_USER_WARNING);
                    return false;
                }
                fclose($fp);
                chmod($this->sqlite_database, 0666);
            }

            $this->sqlite_handle = sqlite_open($this->sqlite_database, 0666, $error);

            if ($this->sqlite_handle !== false) {
                $res = sqlite_query($this->sqlite_handle, "PRAGMA table_info(codes)");

                if (sqlite_num_rows($res) == 0) {
                    $res = sqlite_query(
                            $this->sqlite_handle,
                            "CREATE TABLE codes (id VARCHAR(40) PRIMARY KEY, ip VARCHAR(32),
                             code VARCHAR(32) NOT NULL, code_display VARCHAR(32) NOT NULL,
                             namespace VARCHAR(32) NOT NULL, created INTEGER)"
                    );
                }

                if (mt_rand(0, 100) / 100.0 == 1.0) {
                    // randomly purge old codes
                    $this->purgeOldCodesFromDatabase();
                }
            }

            return $this->sqlite_handle != false;
        }

        return $this->sqlite_handle;
    }

    /**
     * Get a code from the sqlite database for ip address/captchaId.
     *
     * @return string|array Empty string if no code was found or has expired,
     * otherwise returns the stored captcha code.  If a captchaId is set, this
     * returns an array with indices "code" and "code_disp"
     */
    protected function getCodeFromDatabase()
    {
        $code = '';

        if ($this->use_sqlite_db && $this->sqlite_handle !== false) {
            if (Securimage::$_captchaId !== null) {
                $query = "SELECT * FROM codes WHERE id = '" . sqlite_escape_string(Securimage::$_captchaId) . "'";
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
                $ns = sqlite_escape_string($this->namespace);
                $query = "SELECT * FROM codes WHERE ip = '$ip' AND namespace = '$ns'";
            }

            $res = sqlite_query($this->sqlite_handle, $query);
            if ($res && sqlite_num_rows($res) > 0) {
                $res = sqlite_fetch_array($res);

                if ($this->isCodeExpired($res['created']) == false) {
                    if (Securimage::$_captchaId !== null) {
                        // return an array when using captchaId
                        $code = array('code'      => $res['code'],
                                      'code_disp' => $res['code_display']);
                    } else {
                        // return only the code if no captchaId specified
                        $code = $res['code'];
                    }
                }
            }
        }
        return $code;
    }

    /**
     * Remove an entered code from the database
     */
    protected function clearCodeFromDatabase()
    {
        if (is_resource($this->sqlite_handle)) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $ns = sqlite_escape_string($this->namespace);

            sqlite_query($this->sqlite_handle, "DELETE FROM codes WHERE ip = '$ip' AND namespace = '$ns'");
        }
    }

    /**
     * Deletes old codes from sqlite database
     */
    protected function purgeOldCodesFromDatabase()
    {
        if ($this->use_sqlite_db && $this->sqlite_handle !== false) {
            $now   = time();
            $limit = (!is_numeric($this->expiry_time) || $this->expiry_time < 1) ? 86400 : $this->expiry_time;

            sqlite_query($this->sqlite_handle, "DELETE FROM codes WHERE $now - created > $limit");
        }
    }

    /**
     * Checks to see if the captcha code has expired and cannot be used
     * @param unknown_type $creation_time
     */
    protected function isCodeExpired($creation_time)
    {
        $expired = true;

        if (!is_numeric($this->expiry_time) || $this->expiry_time < 1) {
            $expired = false;
        } else if (time() - $creation_time < $this->expiry_time) {
            $expired = false;
        }

        return $expired;
    }

    /**
     * Generate a wav file given the $letters in the code
     * @todo Add ability to merge 2 sound files together to have random background sounds
     * @param array $letters
     * @return string The binary contents of the wav file
     */
    protected function generateWAV($letters)
    {
        $wavCaptcha = new WavFile();
        $first      = true;     // reading first wav file

        foreach ($letters as $letter) {
            $letter = strtoupper($letter);

            try {
                $l = new WavFile($this->audio_path . '/' . $letter . '.wav');

                if ($first) {
                    // set sample rate, bits/sample, and # of channels for file based on first letter
                    $wavCaptcha->setSampleRate($l->getSampleRate())
                               ->setBitsPerSample($l->getBitsPerSample())
                               ->setNumChannels($l->getNumChannels());
                    $first = false;
                }

                // append letter to the captcha audio
                $wavCaptcha->appendWav($l);

                // random length of silence between $audio_gap_min and $audio_gap_max
                if ($this->audio_gap_max > 0 && $this->audio_gap_max > $this->audio_gap_min) {
                    $wavCaptcha->insertSilence( mt_rand($this->audio_gap_min, $this->audio_gap_max) / 1000.0 );
                }
            } catch (Exception $ex) {
                // failed to open file, or the wav file is broken or not supported
                // 2 wav files were not compatible, different # channels, bits/sample, or sample rate
                throw $ex;
            }
        }

        /********* Set up audio filters *****************************/
        $filters = array();

        if ($this->audio_use_noise == true) {
            // use background audio - find random file
            $noiseFile = $this->getRandomNoiseFile();

            if ($noiseFile !== false && is_readable($noiseFile)) {
                try {
                    $wavNoise = new WavFile($noiseFile, false);
                } catch(Exception $ex) {
                    throw $ex;
                }

                // start at a random offset from the beginning of the wavfile
                // in order to add more randomness
                $randOffset = 0;
                if ($wavNoise->getNumBlocks() > 2 * $wavCaptcha->getNumBlocks()) {
                    $randBlock = rand(0, $wavNoise->getNumBlocks() - $wavCaptcha->getNumBlocks());
                    $wavNoise->readWavData($randBlock * $wavNoise->getBlockAlign(), $wavCaptcha->getNumBlocks() * $wavNoise->getBlockAlign());
                } else {
                    $wavNoise->readWavData();
                    $randOffset = rand(0, $wavNoise->getNumBlocks() - 1);
                }


                $mixOpts = array('wav'  => $wavNoise,
                                 'loop' => true,
                                 'blockOffset' => $randOffset);

                $filters[WavFile::FILTER_MIX]       = $mixOpts;
                $filters[WavFile::FILTER_NORMALIZE] = $this->audio_mix_normalization;
            }
        }

        if ($this->degrade_audio == true) {
            // add random noise.
            // any noise level below 95% is intensely distorted and not pleasant to the ear
            $filters[WavFile::FILTER_DEGRADE] = rand(95, 98) / 100.0;
        }

        if (!empty($filters)) {
            $wavCaptcha->filter($filters);  // apply filters to captcha audio
        }

        return $wavCaptcha->__toString();
    }

    public function getRandomNoiseFile()
    {
        $return = false;

        if ( ($dh = opendir($this->audio_noise_path)) !== false ) {
            $list = array();

            while ( ($file = readdir($dh)) !== false ) {
                if ($file == '.' || $file == '..') continue;
                if (strtolower(substr($file, -4)) != '.wav') continue;

                $list[] = $file;
            }

            closedir($dh);

            if (sizeof($list) > 0) {
                $file   = $list[array_rand($list, 1)];
                $return = $this->audio_noise_path . DIRECTORY_SEPARATOR . $file;
            }
        }

        return $return;
    }

    /**
     * Return a wav file saying there was an error generating file
     *
     * @return string The binary audio contents
     */
    protected function audioError()
    {
        return @file_get_contents(dirname(__FILE__) . '/audio/error.wav');
    }

    /**
     * Checks to see if headers can be sent and if any error has been output to the browser
     *
     * @return bool true if headers haven't been sent and no output/errors will break audio/images, false if unsafe
     */
    protected function canSendHeaders()
    {
        if (headers_sent()) {
            // output has been flushed and headers have already been sent
            return false;
        } else if (strlen((string)ob_get_contents()) > 0) {
            // headers haven't been sent, but there is data in the buffer that will break image and audio data
            return false;
        }

        return true;
    }

    /**
     * Return a random float between 0 and 0.9999
     *
     * @return float Random float between 0 and 0.9999
     */
    function frand()
    {
        return 0.0001 * rand(0,9999);
    }

    /**
     * Convert an html color code to a Securimage_Color
     * @param string $color
     * @param Securimage_Color $default The defalt color to use if $color is invalid
     */
    protected function initColor($color, $default)
    {
        if ($color == null) {
            return new Securimage_Color($default);
        } else if (is_string($color)) {
            try {
                return new Securimage_Color($color);
            } catch(Exception $e) {
                return new Securimage_Color($default);
            }
        } else if (is_array($color) && sizeof($color) == 3) {
            return new Securimage_Color($color[0], $color[1], $color[2]);
        } else {
            return new Securimage_Color($default);
        }
    }

    /**
     * Error handler used when outputting captcha image or audio.
     * This error handler helps determine if any errors raised would
     * prevent captcha image or audio from displaying.  If they have
     * no effect on the output buffer or headers, true is returned so
     * the script can continue processing.
     * See https://github.com/dapphp/securimage/issues/15
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @param array $errcontext
     * @return boolean true if handled, false if PHP should handle
     */
    public function errorHandler($errno, $errstr, $errfile = '', $errline = 0, $errcontext = array())
    {
        // get the current error reporting level
        $level = error_reporting();

        // if error was supressed or $errno not set in current error level
        if ($level == 0 || ($level & $errno) == 0) {
            return true;
        }

        return false;
    }
}


/**
 * Color object for Securimage CAPTCHA
 *
 * @version 3.0
 * @since 2.0
 * @package Securimage
 * @subpackage classes
 *
 */
class Securimage_Color
{
    public $r;
    public $g;
    public $b;

    /**
     * Create a new Securimage_Color object.<br />
     * Constructor expects 1 or 3 arguments.<br />
     * When passing a single argument, specify the color using HTML hex format,<br />
     * when passing 3 arguments, specify each RGB component (from 0-255) individually.<br />
     * $color = new Securimage_Color('#0080FF') or <br />
     * $color = new Securimage_Color(0, 128, 255)
     *
     * @param string $color
     * @throws Exception
     */
    public function __construct($color = '#ffffff')
    {
        $args = func_get_args();

        if (sizeof($args) == 0) {
            $this->r = 255;
            $this->g = 255;
            $this->b = 255;
        } else if (sizeof($args) == 1) {
            // set based on html code
            if (substr($color, 0, 1) == '#') {
                $color = substr($color, 1);
            }

            if (strlen($color) != 3 && strlen($color) != 6) {
                throw new InvalidArgumentException(
                  'Invalid HTML color code passed to Securimage_Color'
                );
            }

            $this->constructHTML($color);
        } else if (sizeof($args) == 3) {
            $this->constructRGB($args[0], $args[1], $args[2]);
        } else {
            throw new InvalidArgumentException(
              'Securimage_Color constructor expects 0, 1 or 3 arguments; ' . sizeof($args) . ' given'
            );
        }
    }

    /**
     * Construct from an rgb triplet
     * @param int $red The red component, 0-255
     * @param int $green The green component, 0-255
     * @param int $blue The blue component, 0-255
     */
    protected function constructRGB($red, $green, $blue)
    {
        if ($red < 0)     $red   = 0;
        if ($red > 255)   $red   = 255;
        if ($green < 0)   $green = 0;
        if ($green > 255) $green = 255;
        if ($blue < 0)    $blue  = 0;
        if ($blue > 255)  $blue  = 255;

        $this->r = $red;
        $this->g = $green;
        $this->b = $blue;
    }

    /**
     * Construct from an html hex color code
     * @param string $color
     */
    protected function constructHTML($color)
    {
        if (strlen($color) == 3) {
            $red   = str_repeat(substr($color, 0, 1), 2);
            $green = str_repeat(substr($color, 1, 1), 2);
            $blue  = str_repeat(substr($color, 2, 1), 2);
        } else {
            $red   = substr($color, 0, 2);
            $green = substr($color, 2, 2);
            $blue  = substr($color, 4, 2);
        }

        $this->r = hexdec($red);
        $this->g = hexdec($green);
        $this->b = hexdec($blue);
    }
}
