<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 */
class JFormFieldModal_Article extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Article';

    public function __construct($form = null)
    {
        parent::__construct($form);
    }

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
        $allowEdit		= ((string) $this->element['edit'] == 'true') ? true : false;
        $allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
		$script[] = '	function jSelectArticle_'.$this->id.'(id, title, catid, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = title;';

        if ($allowEdit)
        {
            $script[] = '		jQuery("#' . $this->id . '_edit").removeClass("hidden");';
        }

        if ($allowClear)
        {
            $script[] = '		jQuery("#' . $this->id . '_clear").removeClass("hidden");';
        }

		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

        // Clear button script
        static $scriptClear;

        if ($allowClear && !$scriptClear)
        {
            $scriptClear = true;

            $script[] = '	function jClearArticle(id) {';
            $script[] = '		document.getElementById(id + "_id").value = "";';
            $script[] = '		document.getElementById(id + "_name").value = "' . htmlspecialchars(JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_AN_ARTICLE', true), ENT_COMPAT, 'UTF-8') . '";';
            $script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
            $script[] = '		if (document.getElementById(id + "_edit")) {';
            $script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
            $script[] = '		}';
            $script[] = '		return false;';
            $script[] = '	}';
        }

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


		// Setup variables for display.
		$html	= array();
		$link	= 'index.php?option=com_tz_portfolio_plus&amp;view=articles&amp;layout=modal&amp;tmpl=component&amp;function=jSelectArticle_'.$this->id;

        if (isset($this->element['language']))
        {
            $link .= '&amp;forcedLanguage=' . $this->element['language'];
        }

		$db	= JFactory::getDBO();
		$db->setQuery(
			'SELECT title' .
			' FROM #__tz_portfolio_plus_content' .
			' WHERE id = '.(int) $this->value
		);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_AN_ARTICLE');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// The current user display field.
		// The current tag display field.
        $html[] = '<div class="input-append">';
        
		$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$title.'" disabled="disabled" size="35" />';

        $title      = JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ARTICLE');
        $textLink   = '<i class="icon-file"></i>&nbsp;'.JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ARTICLE_BUTTON');
        $class      = 'modal btn';

        // The active article id field.
        if (0 == (int) $this->value)
        {
            $value = '';
        }
        else
        {
            $value = (int) $this->value;
        }
        
		// The user select button.
		$html[] = '	<a class="modal btn" title="'.$title.'"'
            .' href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            .$textLink.'</a>';

        // Edit article button
        if ($allowEdit)
        {
            $html[] = '<a class="btn hasTooltip' . ($value ? '' : ' hidden') . '" href="index.php?option=com_tz_portfolio_plus&task=article.edit&id=' . $value . '" target="_blank" title="' . JHtml::tooltipText('COM_TZ_PORTFOLIO_PLUS_EDIT_ARTICLE') . '" ><span class="icon-edit"></span> ' . JText::_('JACTION_EDIT') . '</a>';
        }

        // Clear article button
        if ($allowClear)
        {
            $html[] = '<button id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearArticle(\'' . $this->id . '\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</button>';
        }

		$html[] = '</div>';

		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return implode("\n", $html);
	}
}
