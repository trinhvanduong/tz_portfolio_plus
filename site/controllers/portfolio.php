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

// no direct access
defined('_JEXEC') or die;

//jimport('joomla.application.component.controller');

class TZ_Portfolio_PlusControllerPortfolio extends TZ_Portfolio_PlusControllerLegacy
{
    public function getModel($name = 'Portfolio', $prefix = 'TZ_Portfolio_PlusModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    function ajax(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();
        $vName      = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');
        $sublayout  = 'item';

        if(strpos($viewLayout,':')) {
            list($layout, $sublayout) = explode(':',$viewLayout);
        }

        if($view = $this->getView($vName, $viewType, '', array('layout' => $layout))) {

            // Get/Create the model
            if ($model = $this->getModel($vName)) {
                if (!$model->ajax()) {
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            // Display the view
            ob_start();
            $view->display($sublayout);
            $content    = ob_get_contents();
            ob_end_clean();
            echo str_replace('</script>','<\\/script>',$content);
        }
        die();
    }

    function ajaxtags(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();

        if($view = $this->getView('portfolio', $viewType)) {

            // Get/Create the model
            if ($model = $this->getModel('portfolio')) {
                if (!$tags = $model -> ajaxtags()) {
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);

                $view -> assign('itemTags',$tags);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            // Display the view
            echo $view->loadTemplate('filter_tags');
        }
        die();
    }

    function ajaxcategories(){

        $document   = JFactory::getDocument();
        $viewType   = $document->getType();

        if($view = $this->getView('portfolio', $viewType)) {

            // Get/Create the model
            if ($model = $this->getModel('portfolio')) {
                if (!$catids = $model -> ajaxCategories()) {
                    die();
                }

                // Push the model into the view (as default)
                $view->setModel($model, true);

                $view -> assign('itemCategories',$catids);
            }

            $view->document = $document;

            JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

            // Display the view
            echo $view->loadTemplate('filter_categories');
        }
        die();
    }

    public function ajaxComments(){
        $model  = $this -> getModel();
        echo $model -> ajaxComments();
        die();
    }
}