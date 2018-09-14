<?php
require_once($CFG->dirroot . '/theme/bootstrap/renderers.php');

class theme_ildcustom_core_renderer extends theme_bootstrap_core_renderer {

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link core_renderer::block_contents} object.
     *
     * <div id="inst{$instanceid}" class="block_{$blockname} block">
     *      <div class="header"></div>
     *      <div class="content">
     *          ...CONTENT...
     *          <div class="footer">
     *          </div>
     *      </div>
     *      <div class="annotation">
     *      </div>
     * </div>
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if (!empty($bc->blockinstanceid)) {
            $bc->attributes['data-instanceid'] = $bc->blockinstanceid;
        }
        $skiptitle = strip_tags($bc->title);
        if ($bc->blockinstanceid && !empty($skiptitle)) {
            $bc->attributes['aria-labelledby'] = 'instance-' . $bc->blockinstanceid . '-header';
        } else if (!empty($bc->arialabel)) {
            $bc->attributes['aria-label'] = $bc->arialabel;
        }
        if ($bc->dockable) {
            $bc->attributes['data-dockable'] = 1;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls)) {
            $bc->add_class('block_with_controls');
        }

        $classes = $bc->attributes['class'];
        $blocks = array(
            'block_settings',
            'block_navigation'
        );
        
        foreach ($blocks as $block) {
            if (strpos($classes, $block) !== false) $bc->add_class('dock_on_load');
        }


        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::link('#', get_string('skipa', 'access', $skiptitle),
                array('class' => 'skip skip-block', 'id' => 'fsb-' . $bc->skipid,
                    'data-target' => '#sb-' . $bc->skipid));
            $skipdest = html_writer::span('', 'skip-block-to',
                array('id' => 'sb-' . $bc->skipid, 'tabindex' => '-1'));
        }

        $output .= html_writer::start_tag('div', $bc->attributes);

        $output .= $this->block_header($bc);
        $output .= $this->block_content($bc);

        $output .= html_writer::end_tag('div');

        $output .= $this->block_annotation($bc);

        $output .= $skipdest;

        $this->init_block_hider_js($bc);
        return $output;
    }

    //end class
}