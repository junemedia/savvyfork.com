<?php
/**
 * ContactEnhanced Image Helper for Joomla 1.7+
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!class_exists('ceImageHelper')) {

    jimport('joomla.filesystem.file');
    jimport('joomla.filesystem.folder');
    /* Image Class, using for render thumb image or crop image from orginal image. */
    class ceImageHelper
    {
    	protected static $instance = null;
        /**
         * Identifier of the cache path.
         *
         * @access private
         * @param string $__cachePath
         */
        public  $__cachePath;

        /**
         * Identifier of the path of source.
         *
         * @access private
         * @param string $__imageBase
         */
        public $__imageBase;

        /**
         * Identifier of the image's extensions
         *
         * @access public
         * @param array $types
         */
        public $types = array();

        /**
         * Identifier of the quantity of thumnail image.
         *
         * @access public
         * @param string $__quality
         */
        public $__quality = 90;

        /**
         * Identifier of the url of folder cache.
         *
         * @access public
         * @param string $__cacheURL
         */
        public $__cacheURL;


        /**
         * constructor
         */
        public function __construct()
        {
            $this->types = array(1 => "gif", "jpeg", "png", "swf", "psd", "wbmp");
            $this->__imageBase = JPATH_SITE .'/images/';
            $this->__cachePath = $this->__imageBase . 'resized/';
            $this->__cacheURL = 'images/resized/';
        }


        /**
         * constructor
         */
        public function ceImageHelper()
        {
            $this->__construct();
        }


        /**
         * get a instance of ceImageHelper object.
         *
         * This method must be invoked as:
         * <pre>  $ceImage = &ceImageHelper::getInstace();</pre>
         *
         * @static.
         * @access public,
         */
        public static function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new ceImageHelper();
            }
            return self::$instance;
        }


        /**
         * crop or resize image
         *
         *
         * @param string $image path of source.
         * @param integer $width width of thumnail
         * @param integer $height height of thumnail
         * @param boolean $aspect whether to render thumnail base on the ratio
         * @param boolean $crop whether to use crop image to render thumnail.
         * @access public,
         */
        function resize($image, $width, $height, $crop = true, $aspect = true)
        {
            // get image information


            if (!$width || !$height)
                return '';

            $image = str_replace(JURI::base(), '', $image);

            $imagSource = JPATH_SITE . '/' . str_replace('/', DIRECTORY_SEPARATOR, $image);

            if (!file_exists($imagSource) || !is_file($imagSource)) {
                return '';
            }
            $size = getimagesize($imagSource);
            // if it's not an image.
            if (!$size) {
                return '';
            }

            // case 1: render image base on the ratio of source.
            $x_ratio = $width / $size[0];
            $y_ratio = $height / $size[1];

            // set dst, src
            $dst = new stdClass();
            $src = new stdClass();
            $src->y = $src->x = 0;
            $dst->y = $dst->x = 0;

            if ($width > $size[0])
                $width = $size[0];
            if ($height > $height)
                $height = $size[1];

            if ($crop) { // processing crop image
                $dst->w = $width;
                $dst->h = $height;
                if (($size[0] <= $width) && ($size[1] <= $height)) {
                    $src->w = $width;
                    $src->h = $height;
                } else {
                    if ($x_ratio < $y_ratio) {
                        $src->w = ceil($width / $y_ratio);
                        $src->h = $size[1];
                    } else {
                        $src->w = $size[0];
                        $src->h = ceil($height / $x_ratio);
                    }
                }
                $src->x = floor(($size[0] - $src->w) / 2);
                $src->y = floor(($size[1] - $src->h) / 2);
            } else { // processing resize image.
                $src->w = $size[0];
                $src->h = $size[1];
                if ($aspect) { // using ratio
                    if (($size[0] <= $width) && ($size[1] <= $height)) {
                        $dst->w = $size[0];
                        $dst->h = $size[1];
                    } else if (($size[0] <= $width) && ($size[1] <= $height)) {
                        $dst->w = $size[0];
                        $dst->h = $size[1];
                    } else if (($x_ratio * $size[1]) < $height) {
                        $dst->h = ceil($x_ratio * $size[1]);
                        $dst->w = $width;
                    } else {
                        $dst->w = ceil($y_ratio * $size[0]);
                        $dst->h = $height;
                    }
                } else { // resize image without the ratio of source.
                    $dst->w = $width;
                    $dst->h = $height;
                }
            }
			$dst->w = (int) $dst->w;
			$dst->h = (int) $dst->h;
            //
            $ext = substr(strrchr($image, '.'), 1);
            $thumnail = substr($image, 0, strpos($image, '.')) . "_{$width}_{$height}." . $ext;
            $imageCache = $this->__cachePath . str_replace('/', DIRECTORY_SEPARATOR, $thumnail);

            if (file_exists($imageCache)) {
                $smallImg = getimagesize($imageCache);
                if (($smallImg[0] == $dst->w && $smallImg[1] == $dst->h)) {
                    return $this->__cacheURL . $thumnail;
                }
            }

            if (!file_exists($this->__cachePath) && !JFolder::create($this->__cachePath)) {
                return '';
            }

            if (!$this->makeDir($image)) {
                return '';
            }

            // resize image
            $this->_resizeImage($imagSource, $src, $dst, $size, $imageCache);

            return $this->__cacheURL . $thumnail;
        }


        /**
         * render image from other server. // this is pending.
         *
         * @param string $url the url of image.
         * @param array $host contain server information ( using parse_url() function to return this value )
         * @access public,
         */
        function resizeLinkedImage($url, $host)
        {

            if (!is_dir($this->__imageBase .'/linked_images/')) {
                if (!mkdir($this->__imageBase .'/linked_images/', 0755)) {
                    return '';
                }
            }
            //	mkdir($this->__imageBase .'/linked_images/' . $host['host'] . DS, 0755);
            //
            $filePath = $this->__imageBase . 'linked_images/' . $host['host'] . '/' . 'testthu.jpg';
            JFile::exists($filePath);
            $source = file_get_contents($url);
            JFile::write($filePath, $source);
            $files = 'images/linked_images/' . $host['host'] . '/testthu.jpg';

            $output = $this->resize($files, 160, 80);

            //	if( $this->_storeImage ){
            //	JFile::delete( $filePath  );
            //	}


            return $output;
        }


        /**
         * check the folder is existed, if not make a directory and set permission is 755
         *
         *
         * @param array $path
         * @access public,
         * @return boolean.
         */
        function makeDir($path)
        {
            $folders = explode('/', ($path));
            $tmppath = $this->__cachePath;
            for ($i = 0; $i < count($folders) - 1; $i++) {
                if (!file_exists($tmppath . $folders[$i]) && !mkdir($tmppath . $folders[$i], 0755)) {
                    return false;
                }
                $tmppath = $tmppath . $folders[$i].'/';
            }
            return true;
        }


        /**
         * process render image
         *
         * @param string $imageSource is path of the image source.
         * @param stdClass $src the setting of image source
         * @param stdClass $dst the setting of image dts
         * @param string $imageCache path of image cache ( it's thumnail).
         * @access public,
         */
        function _resizeImage($imageSource, $src, $dst, $size, $imageCache)
        {
            // create image from source.
            $extension = $this->types[$size[2]];
            $image = call_user_func("imagecreatefrom" . $extension, $imageSource);

            if (function_exists("imagecreatetruecolor") && ($newimage = imagecreatetruecolor($dst->w, $dst->h))) {

                if ($extension == 'gif' || $extension == 'png') {
                    imagealphablending($newimage, false);
                    imagesavealpha($newimage, true);
                    $transparent = imagecolorallocatealpha($newimage, 255, 255, 255, 127);
                    imagefilledrectangle($newimage, 0, 0, $dst->w, $dst->h, $transparent);
                }

                imagecopyresampled($newimage
                					, $image, $dst->x, $dst->y, $src->x, $src->y, $dst->w, $dst->h, $src->w, $src->h);
            } else {
                $newimage = imagecreate($src->w, $src->h);
                imagecopyresized($newimage, $image, $dst->x, $dst->y, $src->x, $src->y, $dst->w, $dst->h, $size[0], $size[1]);
            }

            switch ($extension) {
                case 'jpeg':
                    call_user_func('image' . $extension, $newimage, $imageCache, $this->__quality);
                    break;
                default:
                    call_user_func('image' . $extension, $newimage, $imageCache);
                    break;
            }
            // free memory
            imagedestroy($image);
            imagedestroy($newimage);
        }


        /**
         * set quality image will render.
         */
        function setQuality($number = 9)
        {
            $this->__quality = $number;
        }


        /**
         * check the image is a linked image from other server.
         *
         *
         * @param string the url of image.
         * @access public,
         * @return array if it' linked image, return false if not
         */
        function isLinkedImage($imageURL)
        {
            $parser = parse_url($imageURL);
            return strpos(JURI::base(), $parser['host']) ? false : $parser;
        }


        /**
         * check the file is a image type ?
         *
         * @param string $ext
         * @return boolean.
         */
        function isImage($ext = '')
        {
            return in_array($ext, $this->types);
        }


        /**
         * check the image source is existed ?
         *
         * @param string $imageSource the path of image source.
         * @access public,
         * @return boolean,
         */
        function sourceExited($imageSource)
        {

            if ($imageSource == '' || $imageSource == '..' || $imageSource == '.') {
                return false;
            }
            $imageSource = str_replace(JURI::base(), '', $imageSource);
            $imageSource = rawurldecode($imageSource);
            return (file_exists(JPATH_SITE . '/' . $imageSource));
        }


        /**
         * check the image source is existed ?
         *
         * @param string $imageSource the path of image source.
         * @access public,
         * @return boolean,
         */
        function parseImage($text)
        {
            $regex = "/\<img.+src\s*=\s*\"([^\"]*)\"[^\>]*\>/";
            preg_match($regex, $text, $matches);
            $images = (count($matches)) ? $matches : array();
            $image = count($images) > 1 ? $images[1] : '';
            return $image;
        }
    }
    

    /**
     *
     * Render image before display it
     * @param string $title
     * @param string $link
     * @param string $image
     * @param object $params
     * @param int $width
     * @param int $height
     * @param string $attrs
     * @param string $returnURL
     * @return string image
     */
    function ceRenderImage($title, $image, $params, $width = 0, $height = 0, $link=null, $attrs = '', $returnURL = false)
    {
        if ($image) {
            $title = strip_tags($title);
            $thumbnailMode = $params->get('thumbnail_mode', 'crop');
            $aspect = $params->get('thumbnail_mode-resize-use_ratio', '1');
            $crop = $thumbnailMode == 'crop' ? true : false;
            $ceImage = ceImageHelper::getInstance();
            
            if (is_array($attrs))
            {
            	$attrs = JArrayHelper::toString($attrs);
            }

            if ($thumbnailMode != 'none' && $ceImage->sourceExited($image)) {
            	
                $imageURL = $ceImage->resize($image, $width, $height, $crop, $aspect);
                if ($returnURL) {
                    return $imageURL;
                }
                if ($imageURL != $image && $imageURL) {
                    $width = $width ? "width=\"$width\"" : "";
                    $height = $height ? "height=\"$height\"" : "";
                    $image = "<img src=\"$imageURL\"  alt=\"{$title}\" title=\"{$title}\" $width $height $attrs />";
                } else {
                    $image = "<img $attrs src=\"$image\"  $attrs  alt=\"{$title}\" title=\"{$title}\" />";
                }
            } else {
                if ($returnURL) {
                    return $image;
                }
                $width = $width ? "width=\"$width\"" : "";
                $height = $height ? "height=\"$height\"" : "";
                $image = "<img $attrs src=\"$image\" alt=\"{$title}\"   title=\"{$title}\" $width $height />";
            }
        } else {
            $image = '';
        }
        if($link){
        	$image = '<a href="' . $link . '" title="" class="ce-thumbnail-link">' . $image . '</a>';
        }
        // clean up globals
        return $image;
    }
}
?>