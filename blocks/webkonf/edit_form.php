<?php 
class block_webkonf_edit_form extends block_edit_form {     
	protected function specific_definition($mform) {         
		     
		$mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));    

        $showoptions = array();
        $showoptions['yes']=get_string('showrecording_yes', 'block_webkonf');
        $showoptions['no']=get_string('showrecording_no', 'block_webkonf');

        $mform->addElement('select', 'config_showrecordings', get_string('configlabel_showrecordings', 'block_webkonf'), $showoptions);
        $mform->setDefault('config_showrecordings', 'yes');        
        
        
		  
	}
}