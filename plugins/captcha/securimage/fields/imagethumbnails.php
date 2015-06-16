<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2010 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		com_contactenhanced
* @since		1.6
 */

class JFormFieldImagethumbnails extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Imagethumbnails';

	function getInput()
	{
		jimport( 'joomla.filesystem.folder' );
		jimport( 'joomla.filesystem.file' );

		// path to images directory
		$path		= JPATH_ROOT.'/'.$this->element['directory'];
		$name		= $this->name;
		//$filter		= $node->attributes('filter');
		$filter = '\.png$|\.gif$|\.jpg$|\.bmp$|\.ico$';
		$exclude	= $this->element['exclude'];
		$stripExt	= $this->element['stripext'];
		$files		= JFolder::files($path, $filter);

		$options = array ();

		if (!$this->element['hide_none'])
		{
			$options[] = JHTML::_('select.option', '-1', '- '.JText::_('Do not use').' -');
		}

		if (!$this->element['hide_default'])
		{
			$options[] = JHTML::_('select.option', '', '- '.JText::_('Use default').' -');
		}

		if ( is_array($files) )
		{
			foreach ($files as $file)
			{
				if ($exclude)
				{
					if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $file ))
					{
						continue;
					}
				}
				if ($stripExt)
				{
					$file = JFile::stripExt( $file );
				}
				$options[] = JHTML::_('select.option', $file, $file);
			}
		}
		$thumbImage		= $name.'-thumbnail-image';
		$thumbContainer	= $name.'-thumbnail-container';
		$javascript=" onchange=\"$('".$thumbImage."').src='"
			.JURI::root().$this->element['directory']."'+this.value;\" ";
		return JHtml::_('select.genericlist', $options, $this->name, 'class="inputbox"'.$javascript, 'value', 'text', $this->value, $this->id)
				.'<span id="'.$thumbContainer.'"><img id="'.$thumbImage.'" src="'
				.JURI::root().$this->element['directory'].$this->value.'" /></span>'
				;
	}
}
