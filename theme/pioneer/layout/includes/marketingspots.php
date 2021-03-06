<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is built using the bootstrapbase template to allow for new theme's using
 * Moodle's new Bootstrap theme engine
 *
 * @package    theme_pioneer
 * @copyright  2015 Chris Kenniburg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
?>
<div class="clearfix"></div>
<div class="row-fluid" id="marketing-spots">
<?php if ($PAGE->theme->settings->marketing1) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketing1image) { echo $PAGE->theme->setting_file_url('marketing1image', 'marketing1image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwrap">
        <div class="market-spot">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketing1 ?></h3>
            </div>
            <?php echo $PAGE->theme->settings->marketing1content ?>
            
            </div>
            <?php if ($PAGE->theme->settings->marketing1buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketing1buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketing1target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketing1icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketing1buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($PAGE->theme->settings->marketing2) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketing2image) { echo $PAGE->theme->setting_file_url('marketing2image', 'marketing2image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;"  class="marketwrap">
        <div class="market-spot">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketing2 ?></h3>
            </div>
            <?php echo $PAGE->theme->settings->marketing2content ?>
            
            </div>
            <?php if ($PAGE->theme->settings->marketing2buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketing2buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketing2target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketing2icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketing2buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($PAGE->theme->settings->marketing3) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketing3image) { echo $PAGE->theme->setting_file_url('marketing3image', 'marketing3image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwrap">
        <div class="market-spot">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketing3 ?></h3>
            </div>
            <?php echo $PAGE->theme->settings->marketing3content ?>
            
            </div>
            <?php if ($PAGE->theme->settings->marketing3buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketing3buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketing3target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketing3icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketing3buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

</div>
