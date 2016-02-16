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

// No direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusTemplate {

    public static function getTemplate($params = false)
    {
        $templateId = self::getTemplateId();
        $template   = new stdClass;

        $input  = JFactory::getApplication() -> input;

        JTable::addIncludePath(COM_TZ_PORTFOLIO_PLUS_ADMIN_PATH.DIRECTORY_SEPARATOR.'tables');

        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;
        $app                    = JFactory::getApplication('site');
        $input                  = $app -> input;
        $view_layout            = true;

        $db                     = JFactory::getDbo();
        $query                  = $db -> getQuery(true);
        $query -> select('COUNT(t.id)');
        $query -> from('#__tz_portfolio_plus_templates AS t');
        $query -> join('INNER', '#__tz_portfolio_plus_extensions AS e On e.element = t.template');
        $query -> where('e.type = '.$db -> quote('tz_portfolio_plus-template'));
        $query -> where('e.published = 1');
        $query -> where('t.id =' . $templateId);
        $query -> group('t.id');
        $db -> setQuery($query);
        if(!$db -> loadResult()){
            $templateId = null;
        }

        if($templateId){
            $table -> load($templateId);
            $template -> id         = $templateId;
            $template -> template   = $table -> template;
            if($table -> params && !empty($table -> params)) {
                $_params    = new JRegistry($table -> params);
                $template->params = $_params;
            }
            if($table -> layout){
                $template -> layout = json_decode($table -> layout);
            }
        }else{
            if($home = $table -> getHome()){
                $template -> id         = $home -> id;
                $template -> template   = $home -> template;
                if($home -> params && !empty($home -> params)) {
                    $_params    = new JRegistry($home -> params);
                    $template->params = $_params;
                }
                if($home -> layout){
                    $template -> layout = json_decode($home -> layout);
                }
            }
        }

        $tplparams      = $template -> params;
        $view_folder    = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH.DIRECTORY_SEPARATOR
        . $template->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
        . $tplparams -> get('layout','default') . DIRECTORY_SEPARATOR . $input -> get('view');

        if(!JFolder::exists($view_folder) && !$tplparams -> get('use_single_layout_builder',1)){
            if($home = $table -> getHome()){
                $default_params = new JRegistry;
                $default_params -> loadString($home -> params);
                $default_layout = 'default';
                if($default_params) {
                    $default_layout = $default_params->get('layout', 'default');
                }
                    $view_folder = COM_TZ_PORTFOLIO_PLUS_TEMPLATE_PATH . DIRECTORY_SEPARATOR
                        . $home->template . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR
                        . $default_layout . DIRECTORY_SEPARATOR . $input->get('view');
                    if (JFolder::exists($view_folder) || (!JFolder::exists($view_folder)
                            && $default_params -> get('use_single_layout_builder',1))) {
                        $template -> id         = $home -> id;
                        $template -> template   = $home -> template;
                        $template -> params     = $default_params;
                        if($home -> layout){
                            $template -> layout = json_decode($home -> layout);
                        }
                    }
            }
        }

        if ($params)
        {
            return $template;
        }

        return $template->template;
    }

    public static function getTemplateDefault(){

        $template   = new stdClass;

        $template -> template   = 'system';
        $template -> params     = new JRegistry();
        $template -> layout     = null;
        $table  = JTable::getInstance('Templates','TZ_Portfolio_PlusTable');

        if($home = $table -> getHome()){
            $template -> id         = $home -> id;
            $template -> template   = $home -> template;
            if($home -> params && !empty($home -> params)) {
                $_params    = new JRegistry($home -> params);
                $template->params = $_params;
            }
            if($home -> layout){
                $template -> layout = json_decode($home -> layout);
            }
        }

        return $template;
    }


    protected static function getTemplateId($artId = null,$catId = null){

        $db         = JFactory::getDbo();
        $app        = JFactory::getApplication('site');
        $templateId = null;
        $_catId     = null;
        $_artId     = null;

        $params = $app -> getParams();
        $templateId = $params -> get('tz_template_style_id');

        $input  = $app -> input;
        switch($input -> get('view')){
            case 'article':
                case 'p_article':
                $_artId = $input -> get('id',null,'int');
                $artModel   = JModelItem::getInstance('Article','TZ_Portfolio_PlusModel');
                if($artItem    = $artModel -> getItem($_artId)){
                    $_catId = $artItem -> catid;
                }
                break;
        }

        if(!empty($catId)){
            $_catId = $catId;
        }
        if(!empty($artId)){
            $_artId = $artId;
        }

        if($_catId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_categories'));
            $query -> where($db -> quoteName('id').'='.$_catId);
            $db -> setQuery($query);
            if($crow = $db -> loadObject()){
                if($crow -> template_id){
                    $templateId = $crow -> template_id;
                }
            }
        }
        if($_artId){
            $query  = $db -> getQuery(true);
            $query -> select($db -> quoteName('template_id'));
            $query -> from($db -> quoteName('#__tz_portfolio_plus_content'));
            $query -> where($db -> quoteName('id').'='.$_artId);
            $db -> setQuery($query);
            if($row = $db -> loadObject()){
                if($row -> template_id){
                    $templateId = $row -> template_id;
                }
            }
        }
        if(!$templateId){

        }
        return (int) $templateId;
    }
}