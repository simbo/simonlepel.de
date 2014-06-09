<?php
/**
 * Determines whether or not to display a template part based on an array of conditional tags or page templates.
 *
 * If any of the is_* conditional tags or is_page_template(template_file) checks return true, the template will NOT be displayed.
 *
 * Based on the "Roots Sidebar" technique: http://roots.io/the-roots-sidebar/
 *
 * @param array list of conditional tags (http://codex.wordpress.org/Conditional_Tags)
 * @param array list of page templates. These will be checked via is_page_template()
 *
 * @return boolean True will display the sidebar, False will not
 */

class ConditionalDisplay {

    private $conditionals;
    private $templates;

    public $display = false;

    function __construct($conditionals = array(), $templates = array()) {
        $this->conditionals = $conditionals;
        $this->templates = $templates;
        $conditionals = array_map(array($this, 'check_conditional_tag'), $this->conditionals);
        $templates = array_map(array($this, 'check_page_template'), $this->templates);
        $this->display = !(in_array(true, $conditionals) || in_array(true, $templates));
    }

    private function check_conditional_tag($conditional_tag) {
        $func_array = is_array($conditional_tag);
        $func = $func_array ? $conditional_tag[0] : $conditional_tag;
        if( !function_exists($func) )
            return false;
        return $func_array ? $func($conditional_tag[1]) : $func();
    }

    private function check_page_template($page_template) {
        return is_page_template($page_template);
    }

}
