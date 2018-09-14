<?php  
//// This file is part of Moodle - http://moodle.org/
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
 * Analytics Recommendations block
 *
 * @package    contrib
 * @subpackage block_analytics_recommendations
 * @copyright  2012 Cristina Fernández
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Description of my_graphs
 *
 * @author cristina
 */
class my_graph {
    // Colours
    var $colours=array('fuchsia','ltgreen','orange','ltblue','gray','red','yellow',
        'lime','ltorange','ltltblue','purple','ltred','ltltorange','ltltgreen','maroon','aqua','olive','ltltred');
    
    // Points
    var $point=array('none','square','circle','diamond','open-square','open-circle','open-diamond');
    
    // Lines
    var $line = array ('none','line','brush');
    
    // Bars
    var $bar = array ('none','fill', 'open');
    
    // Legend position
    var $legend = array ('none', 'outside-top', 'outside-bottom', 'bottom-left', 'bottom-right',
                      'top-left', 'top-right','outside-left','outside-right');
    
    var $graph;
   
    /**
     * Constructor
     * @param int $width
     * @param int $height 
     */
   public function __construct($width, $height) {
        
        $this->graph = new graph($width, $height);   
        
        $this->graph->parameter['legend']  = $this->legend[1];
        
        $this->graph->parameter['legend_offset']  =  0;
        //$this->graph->parameter['legend_border']     = 'black';

        // Rotation axis x
        $this->graph->parameter['x_axis_angle']  =  30;

        // Separation between legend and graphic
        $this->graph->parameter['outer_padding'] =  6;

        // Title
        $this->graph->parameter['title'] = '';

        // Axes labels
        $this->graph->parameter['label_size'] = '10';
        $this->graph->parameter['label_color'] = 'black';
        $this->graph->parameter['x_label_angle']= 0;
        
        $this->graph->parameter['x_inner_padding'] = 8;  
        $this->graph->parameter['y_inner_padding']= 8;

        $this->graph->parameter['y_min_left'] = 0;
               
        $this->graph->parameter['point_size'] = 8; 
        $this->graph->parameter['bar_size'] = 0.6; 
        
        // Disable shadow
        $this->graph->parameter['shadow'] = 'none'; 
        
        $this->graph->parameter['y_axis_gridlines']= 15;
        
        $this->graph->parameter['y_resolution_left']= 0;
        
        // Decimals in the y-axis [0 - none, 1 to one]
        $this->graph->parameter['y_decimal_left']= 1;

        $this->graph->parameter['x_grid']='dash'; //'line','dash','none' 
        $this->graph->parameter['y_grid']='dash';
    }
    
    /**
     * To set the legend
     * @param $id
     */
    public function set_legend($id){
        $this->graph->parameter['legend']  = $this->legend[$id];
    }
    
    /**
     * It returns a one serie graph
     * @param $xdata
     * @param $ydata
     * @param $legend
     * @param $xlabel
     * @param $ylabel 
     */
    public function one_series_graph($xdata,$ydata,$legend,$xlabel,$ylabel,$colour='green'){
        $this->graph->x_data            = $xdata;
        $this->graph->y_data['data1']   = $ydata;
        
        $this->graph->y_format['data1'] = array(  
                                            'colour' => $colour,
                                            'bar' => 'fill',                                            
                                            'legend' => $legend);   
        $this->graph->y_order = array('data1');

        $this->graph->parameter['x_label']=$xlabel;  

        $this->graph->parameter['y_label_left' ]=$ylabel;      
        
        $max=max($ydata)*1.5;
        if ($max>100) $max=100;
        // Set the max y-axis value
        $this->graph->parameter['y_max_left'] = $max;
        $this->graph->draw();
    }
    
    /**
     * It returns a two serie graph
     * @param $xdata
     * @param $ydata1
     * @param $ydata2
     * @param $legend1
     * @param $legend2
     * @param $xlabel
     * @param $ylabel
     * @param $colours
     * @param $points
     * @param $lines
     * @param $bars 
     */
    public function two_series_graph($xdata,$ydata1,$ydata2,$legend1,$legend2,$xlabel,$ylabel,$colours=array('green','navy'),$points=array(0,1),$lines=array(0,1),$bars=array(1,0)){        
        $this->graph->x_data            = $xdata;
        $this->graph->y_data['data1']   = $ydata1;
        $this->graph->y_data['data2']   = $ydata2;        
     
        $this->graph->y_format['data1'] = array(  
                                            'colour' => $colours[0],
                                            'line' => $this->line[$lines[0]],  
                                            'point' => $this->point[$points[0]], 
                                            'bar' => $this->bar[$bars[0]], 
                                            'legend' => $legend1);
        
        $this->graph->y_format['data2'] = array(  
                                            'colour' => $colours[1], 
                                            'line' => $this->line[$lines[1]],  
                                            'point' => $this->point[$points[1]], 
                                            'bar' => $this->bar[$bars[1]],                                             
                                            'legend' => $legend2);

        $this->graph->y_order = array('data1','data2');

        $this->graph->parameter['x_label']=$xlabel;  

        $this->graph->parameter['y_label_left' ]=$ylabel;      
        
        $max=max(max($ydata1),max($ydata2))*1.5;
        if ($max>100) $max=100;
        // Set the max y-axis value
        $this->graph->parameter['y_max_left'] = $max;
        $this->graph->draw();
    }
    
    /**
     * It returns a multiple serie graph
     * @param $xdata
     * @param $ydatas
     * @param $legends
     * @param $xlabel
     * @param $ylabel
     * @param $points
     * @param $lines
     * @param $bars 
     */
    public function multiple_series_graph($xdata,$ydatas,$legends,$xlabel,$ylabel,$points,$lines,$bars){        
        $this->graph->x_data = $xdata;        
             
        foreach ($ydatas as $key => $ydata){
            $this->graph->y_data[$key]   = $ydata;
            $this->graph->y_format[$key] = array(  
                        'colour' => $this->colours[$key%18], 
                        'line' => $this->line[$lines[$key]],  
                        'point' => $this->point[$points[$key]], 
                        'bar' => $this->bar[$bars[$key]], 
                        'legend' => $legends[$key]);
            // Save the order
            $order[]=$key;
            
            // Save the max
            $maxs[]=max($ydata);
        }
        $this->graph->y_format[$key]['colour']='navy';
        // Set the order
        $this->graph->y_order = $order;
        
        $this->graph->parameter['x_label']=$xlabel;  

        $this->graph->parameter['y_label_left' ]=$ylabel; 
        
        $max=max($maxs)*1.5;
        if ($max>100) $max=100;
        // Set the max y-axis value
        $this->graph->parameter['y_max_left'] = $max;
        $this->graph->draw();
    }   
}
?>
