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

//no direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

$form   = $this -> form;
JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "group.cancel" || document.formvalidator.isValid(document.getElementById("group-form")))
		{
			' . $form->getField("description")->save() . '
			Joomla.submitform(task, document.getElementById("group-form"));
		}
	};
');
?>
<form name="adminForm" method="post" class="form-validate" id="group-form"
      action="index.php?option=com_tz_portfolio_plus&view=group&layout=edit&id=<?php echo $this -> item -> id?>">
    <div class="form-horizontal">
        <fieldset class="adminform">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('JDETAILS');?></a></li>
                <li><a href="#categories_assignment" data-toggle="tab"><?php echo JText::_('COM_TZ_PORTFOLIO_PLUS_CATEGORIES_ASSIGNMENT');?></a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="details">
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('name');?></div>
                        <div class="controls"><?php echo $form -> getInput('name');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('published');?></div>
                        <div class="controls"><?php echo $form -> getInput('published');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('id');?></div>
                        <div class="controls"><?php echo $form -> getInput('id');?></div>
                    </div>
                    <div class="control-group">
                        <div class="control-label"><?php echo $form -> getLabel('description');?></div>
                        <div class="controls"><?php echo $form -> getInput('description');?></div>
                    </div>
                </div>

                <div class="tab-pane assignment" id="categories_assignment">
                    <?php echo $form->getInput('categories_assignment'); ?>
                </div>
            </div>
        </fieldset>

    </div>
    <div class="clr"></div>

    <input type="hidden" value="" name="task">
    <input type="hidden" name="return" value="<?php echo JRequest::getCmd('return');?>" />
    <?php echo JHTML::_('form.token');?>

</form>