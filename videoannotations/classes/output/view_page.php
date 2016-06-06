<?php
// Standard GPL and phpdocs
namespace mod_videoannotations\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class view_page implements renderable, templatable {
    /** @var string $sometext Some text to show how to pass data to a template. */
    var $data = null;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        //$data = new stdClass();
        //$data->tabs = $this->tabs;
        return $this->data;
    }
}
