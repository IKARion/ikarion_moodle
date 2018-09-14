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
<div class="row-fluid" id="marketingtexbox-spots">
<?php if ($PAGE->theme->settings->marketingtexbox1) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketingtexbox1image) { echo $PAGE->theme->setting_file_url('marketingtexbox1image', 'marketingtexbox1image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwraptop">
        <div class="market-spottop">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketingtexbox1 ?></h3>
            </div>
            <p>
            <?php echo $PAGE->theme->settings->marketingtexbox1content ?>
            </p> 
            </div>
            <?php if ($PAGE->theme->settings->marketingtexbox1buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketingtexbox1buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketingtexbox1target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketingtexbox1icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketingtexbox1buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($PAGE->theme->settings->marketingtexbox2) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketingtexbox2image) { echo $PAGE->theme->setting_file_url('marketingtexbox2image', 'marketingtexbox2image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwraptop">
        <div class="market-spottop">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketingtexbox2 ?></h3>
            </div>
            <p>
            <?php echo $PAGE->theme->settings->marketingtexbox2content ?>
            </p> 
            </div>
            <?php if ($PAGE->theme->settings->marketingtexbox2buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketingtexbox2buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketingtexbox2target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketingtexbox2icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketingtexbox2buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($PAGE->theme->settings->marketingtexbox3) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketingtexbox3image) { echo $PAGE->theme->setting_file_url('marketingtexbox3image', 'marketingtexbox3image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwraptop">
        <div class="market-spottop">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketingtexbox3 ?></h3>
            </div>
            <p>
            <?php echo $PAGE->theme->settings->marketingtexbox3content ?>
            </p> 
            </div>
            <?php if ($PAGE->theme->settings->marketingtexbox3buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketingtexbox3buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketingtexbox3target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketingtexbox3icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketingtexbox3buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
</div>


<div class="clearfix"></div>
<div class="row-fluid" id="marketingtexbox-spots">

<?php if ($PAGE->theme->settings->marketingtexbox4) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketingtexbox4image) { echo $PAGE->theme->setting_file_url('marketingtexbox4image', 'marketingtexbox4image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwraptop">
        <div class="market-spottop">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketingtexbox4 ?></h3>
            </div>
            <p>
            <?php echo $PAGE->theme->settings->marketingtexbox4content ?>
            </p> 
            </div>
            <?php if ($PAGE->theme->settings->marketingtexbox4buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketingtexbox4buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketingtexbox4target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketingtexbox4icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketingtexbox4buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($PAGE->theme->settings->marketingtexbox5) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketingtexbox5image) { echo $PAGE->theme->setting_file_url('marketingtexbox5image', 'marketingtexbox5image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwraptop">
        <div class="market-spottop">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketingtexbox5 ?></h3>
            </div>
            <p>
            <?php echo $PAGE->theme->settings->marketingtexbox5content ?>
            </p> 
            </div>
            <?php if ($PAGE->theme->settings->marketingtexbox5buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketingtexbox5buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketingtexbox5target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketingtexbox5icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketingtexbox5buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>

<?php if ($PAGE->theme->settings->marketingtexbox6) { ?>
<div class="span4">
    <div style="background-image: url(<?php if ($PAGE->theme->settings->marketingtexbox6image) { echo $PAGE->theme->setting_file_url('marketingtexbox6image', 'marketingtexbox6image', true); } ?>);background-repeat: no-repeat;background-size:cover; background-position:center;" class="marketwraptop">
        <div class="market-spottop">
            <div class="markettext">
            <div class="market-title">
            <h3><?php echo $PAGE->theme->settings->marketingtexbox6 ?></h3>
            </div>
            <p>
            <?php echo $PAGE->theme->settings->marketingtexbox6content ?>
            </p> 
            </div>
            <?php if ($PAGE->theme->settings->marketingtexbox6buttonurl) { ?>
            <div class="marketlink">
            <a href="<?php echo $PAGE->theme->settings->marketingtexbox6buttonurl ?>" target="<?php echo $PAGE->theme->settings->marketingtexbox6target ?>" class="button"><span><i class="fa fa-<?php echo $PAGE->theme->settings->marketingtexbox6icon ?>"></i></span>
            <?php echo $PAGE->theme->settings->marketingtexbox6buttontext ?>
            </a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php } ?>
</div>

