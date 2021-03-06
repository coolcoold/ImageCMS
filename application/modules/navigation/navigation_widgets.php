<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Image CMS
 *
 * Navigation widgets
 */
class Navigation_Widgets extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function widget_navigation($widget = array()) {
        $this->load->module('core');

        if ($widget['settings'] == FALSE) {
            $settings = $this->defaults;
        } else {
            $settings = $widget['settings'];
        }
        switch ($this->core->core_data['data_type']) {
            case 'category':
                $cur_category = $this->core->cat_content;

                $i = 0;
                $path_count = count($cur_category['path']);

                $path_categories = $this->lib_category->get_category(array_keys($cur_category['path']));

                $tpl_data = array('navi_cats' => $path_categories);

                return $this->template->fetch('widgets/' . $widget['name'], $tpl_data);
                break;

            case 'page':
                if ($this->core->cat_content['id'] > 0) {
                    $cur_category = $this->core->cat_content;

                    $path_categories = $this->lib_category->get_category(array_keys($cur_category['path']));

                    // Insert Page data
                    $path_categories[] = array(
                        'path_url' => $this->core->page_content['cat_url'] . $this->core->page_content['url'],
                        'name' => $this->core->page_content['title']
                    );

                    $tpl_data = array('navi_cats' => $path_categories);

                    return $this->template->fetch('widgets/' . $widget['name'], $tpl_data);
                }
                break;
            case 'shop_category':
                if ($this->core->core_data['id'] != null && $this->core->core_data > 0) {
                    //get category object
                    $ci = &get_instance();
                    $shop_category = $ci->db->select(array('full_path_ids', 'url', 'name'))
                            ->where(array('shop_category.id' => $this->core->core_data['id'],
                                'shop_category_i18n.locale' => BaseAdminController::getCurrentLocale()))
                            ->join('shop_category_i18n', 'shop_category_i18n.id=shop_category.id')
                            ->limit(1)
                            ->get('shop_category');
                    if ($shop_category) {
                        $shop_category = $shop_category->result();
                        $full_path_ids = $shop_category[0]->full_path_ids;
                        $full_path_ids = unserialize($full_path_ids);
                        $result = array();
                        if (is_array($full_path_ids) && !empty($full_path_ids)) {
                            $result = $ci->db->select(array('full_path', 'name'))
                                    ->where('locale', BaseAdminController::getCurrentLocale())
                                    ->where_in('shop_category.id', $full_path_ids)
                                    ->join('shop_category_i18n', 'shop_category_i18n.id=shop_category.id')
                                    ->get('shop_category');
                            if ($result) {
                                $result = $result->result_array();
                                foreach ($result as $key => $value) {
                                    $result[$key]['path_url'] = 'shop/category/' . $result[$key]['full_path'];
                                    unset($result[$key]['url']);
                                }
                                $result[] = array('path_url' => $shop_category[0]->url,
                                    'name' => $shop_category[0]->name);
                            }
                        } else {
                            //current category is first level category
                            $result[] = array('path_url' => $shop_category[0]->url, 'name' => $shop_category[0]->name);
                        }
                        $tpl_data = array('navi_cats' => $result);
                        return $this->template->fetch('widgets/' . $widget['name'], $tpl_data);
                    } else {
                        throw new Exception("Category not found");
                    }
                }
                break;
            case 'product':
                if ($this->core->core_data['id'] != null && $this->core->core_data['id'] > 0) {
                    $ci = &get_instance();
                    //get product model
                    $product = $ci->db->select(array('name', 'category_id'))
                            ->where(array('shop_products.id' => $this->core->core_data['id'],
                                'locale' => BaseAdminController::getCurrentLocale()))
                            ->join('shop_products_i18n', 'shop_products_i18n.id=shop_products.id')
                            ->get('shop_products');
                    if ($product) {
                        $product = $product->result_array();
                        $product = $product[0];
                        //get category path
                        if ($product['category_id'] != null && $product['category_id'] > 0) {
                            $shop_category = $ci->db->select(array('full_path_ids', 'full_path', 'name'))
                                    ->where(array('shop_category.id' => $product['category_id'],
                                        'shop_category_i18n.locale' => BaseAdminController::getCurrentLocale()))
                                    ->join('shop_category_i18n', 'shop_category_i18n.id=shop_category.id')
                                    ->limit(1)
                                    ->get('shop_category');
                            if ($shop_category) {
                                $shop_category = $shop_category->result_array();
                                $shop_category = $shop_category[0];
                                $full_path_ids = $shop_category['full_path_ids'];
                                $full_path_ids = unserialize($full_path_ids);
                                if (is_array($full_path_ids) && !empty($full_path_ids)) {
                                    $result = $ci->db->select(array('full_path', 'name'))
                                            ->where('locale', BaseAdminController::getCurrentLocale())
                                            ->where_in('shop_category.id', $full_path_ids)
                                            ->join('shop_category_i18n', 'shop_category_i18n.id=shop_category.id')
                                            ->get('shop_category');
                                    if ($result) {
                                        $result = $result->result_array();
                                        foreach ($result as $key => $value) {
                                            $result[$key]['path_url'] = 'shop/category/' . $result[$key]['full_path'];
                                            unset($result[$key]['url']);
                                        }
                                        $result[] = array('path_url' => 'shop/category/' . $shop_category['full_path'],
                                            'name' => $shop_category['name']);
                                    }
                                } else {
                                    //current category is first level category
                                    $result[] = array('path_url' => 'shop/category/' . $shop_category['full_path'], 'name' => $shop_category['name']);
                                }
                                $result[] = array('path_url' => '', 'name' => $product['name']);
                                $tpl_data = array('navi_cats' => $result);
                                return $this->template->fetch('widgets/' . $widget['name'], $tpl_data);
                            } else {
                                throw new Exception("Category not found");
                            }
                        }
                    } else {
                        throw new Exception("Product not found");
                    }
                }
                break;
        }
    }

    // Template functions
    function display_tpl($file, $vars = array()) {
        $this->template->add_array($vars);

        $file = realpath(dirname(__FILE__)) . '/templates/' . $file . '.tpl';
        $this->template->display('file:' . $file);
    }

    function fetch_tpl($file, $vars = array()) {
        $this->template->add_array($vars);

        $file = realpath(dirname(__FILE__)) . '/templates/' . $file . '.tpl';
        return $this->template->fetch('file:' . $file);
    }

}

/* End of file widgets.php */
