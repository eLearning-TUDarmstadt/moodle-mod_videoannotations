<?php
// Standard GPL and phpdocs
namespace mod_videoannotations\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;

class renderer extends plugin_renderer_base {
    /**
     * Defer to template.
     *
     * @param index_page $page
     *
     * @return string html for the page
     */
    public function render_view_page($page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('mod_videoannotations/view_page', $data);
    }
}
