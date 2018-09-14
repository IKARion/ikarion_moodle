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
 * Description of gui
 *
 * @author Cristina
 */
class gui {
    
    /**
     * Devuelve un objeto html_table
     * @return html_table
     */
    public function get_legend_table(){
        $colours=array('#FA8072','#F0E68C','#3CB371');
        $titles=array(get_string('low_participation','block_analytics_recommendations'),get_string('half_participation','block_analytics_recommendations'),get_string('high_participation','block_analytics_recommendations'));
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter';
        $row = new html_table_row(); 
        $row->cells = array(); 
        for ($i=0;$i<3;$i++){
            $cell1 = new html_table_cell(); 
            $cell1->style='background-color:'.$colours[$i].';width:10px;height=6px';
            $cell2 = new html_table_cell(); 
            $cell2->style='font-size:10px; text-align:left;';
            $cell2->text=$titles[$i];
            $row->cells[]=$cell1;
            $row->cells[]=$cell2;    
        }
        $table->data = array($row);
        return $table;
    }
    /**
     * Devuelve un objeto html_html
     * @return html_table
     */
    public function get_legend_table_grade(){         
        $colours=array('#FA8072','#FFAAAA','#F0E68C','#90EE90','#3CB371'); 
        $titles=array('[0%,30%)','[30%,50%)','[50%,70%)','[70%,90%)','[90%,100%]');
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter';
        $row = new html_table_row(); 
        $row->cells = array(); 
        for ($i=0;$i<5;$i++){
            $cell1 = new html_table_cell(); 
            $cell1->style='background-color:'.$colours[$i].';width:10px;height=6px';
            $cell2 = new html_table_cell(); 
            $cell2->style='font-size:10px; text-align:left;';
            $cell2->text=$titles[$i];
            $row->cells[]=$cell1;
            $row->cells[]=$cell2;    
        }
        $table->data = array($row);
        return $table;
    }
    
    /**
     * Devuelve un objeto html_html
     * @return html_table
     */
    public function get_reference_course_table($reference_course_name,$reference_course_short_name){          
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter';
        $row = new html_table_row(); 
        $row->cells = array(); 
        $cell1 = new html_table_cell(); 
        $cell1->style='background-color:#C0C0C0;font-size:11px; text-align:left;';
        $cell1->text=get_string('reference_course','block_analytics_recommendations').': '.$reference_course_name.' ('.$reference_course_short_name.') ';
        $row->cells[]=$cell1;
        $table->data = array($row);
        return $table;
    }
    
    /**
          */
  
    /**
     * Devuelve un objeto html_table con las características pasadas como 
     * parámetros (color de fondo de las celdas, encabezados, links, etc.)
     * @param $table_data
     * @param $colours
     * @param $first_column
     * @param $last_row
     * @param $table_links
     * @return html_table 
     */
    public function get_table($table_data,$colours=null,$first_column=false, $last_row=false,$table_links=null){
        $rows = count($table_data,0);
        $cols = (count($table_data,1)/count($table_data,0))-1;        
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter';
        $table->attributes['style'] = 'font-size:11px;';
        
        $table->head=array();
        for($j=0;$j<$cols;$j++){	
            $table->head[]=$table_data[0][$j];
        }
        for($i=1;$i<$rows;$i++){            
            $row = new html_table_row(); 
            $row->cells = array(); 
            for($j=0;$j<$cols;$j++){
                $text=$table_data[$i][$j];
                $cell=new html_table_cell(); 
                if ($j==0){
                    $cell->style='text-align:left';
                    $cell->header=$first_column;
                }else{                    
                    if (isset($colours[$i][$j]))        
                        $cell->style='text-align:center;font-size:11px;background-color:'.$colours[$i][$j];
                    else
                        $cell->style='text-align:center;font-size:11px';
                }
                if (isset($table_links[$i][$j])){
                    $cell->attributes['onclick']=$table_links[$i][$j];
                }
                $cell->text=$text;      
                $row->cells[]=$cell;
            }
            $table->data[] = $row;
        }     
        return $table;           
    }
    
    /**
     * Devuelve un objeto html_table con las características pasadas como 
     * parámetros (color de fondo de las celdas, alineación del texto).
     * @param $table_data
     * @param $colours
     * @param $align
     * @return html_table
     */
    public function get_table_progress($table_data,$colours,$align){        
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter';
        $table->attributes['style'] = 'font-size:11px;';
        $table->head=array();
        for($j=0;$j<count($table_data[0]);$j++){	
            $table->head[]=$table_data[0][$j];            
        }        
        for($i=1;$i<count($table_data);$i++){	
            $row = new html_table_row(); 
            $row->cells = array(); 
            for($j=0;$j<count($table_data[$i]);$j++){ 
                $cell=new html_table_cell();   
                $cell->style='font-size:11px;';
                if (isset($align[$i][$j]))
                    $cell->style.='text-align:'.$align[$i][$j].';';
                if (isset($colours[$i][$j])) 
                    $cell->style.='background-color:'.$colours[$i][$j].';';                
                $cell->text=$table_data[$i][$j]; 
                if ($j!=2)
                    $cell->header=true;
                $row->cells[]=$cell;
            }            
            $table->data[] = $row;
        }     
        return $table;           
    }
    
    /**
     * Develve el color asociado a un determinado porcentaje
     * @param $porcentaje
     * @return string 
     */
    public function get_colour_cell_grade($porcentaje) {
        if ($porcentaje<30)
            $colour='#FA8072';
        else if ($porcentaje<50)
            $colour='#FFA07A';   
        else if ($porcentaje<70)
            $colour='#F0E68C';  
        else if ($porcentaje<90)
            $colour='#90EE90';  
        else
            $colour='#3CB371'; 
        return $colour;
    }
    
    /**
     * Develve el color asociado a un determinado porcentaje
     * @param type $porcentaje
     * @param type $total
     * @return string 
     */
    public function get_colour_cell($porcentaje,$total) {
	$avg=round(100/$total,2);
	$avg60=$avg+($avg*0.6);
	if ($porcentaje=="\0")
  	$res='#FA8072';
	else if (!isset($porcentaje))
		$res='#FFFFFF';
	else { 
		if ($porcentaje>=$avg60)
			$res='#3CB371';	
		else 	if ($porcentaje>=$avg)
                            $res='#F0E68C';	
			else 
                            $res='#FA8072';	 }
	return $res;
    }
    
    /**
     * Devuelve el código HTML necesario para generar el tipo de gráfico 
     * especificado con los datos de participación del alumno del curso pasado 
     * como parámetro.
     * @global object $CFG
     * @param $course
     * @param $type
     * @param $user
     * @param $defaultmod
     * @param $users
     * @return string 
     */
    public function get_analytics_graph($course,$type,$user=null,$defaultmod=null,$users=null){
        global $CFG;
        $cad='<div class="graph" style="margin: 8px auto;text-align:center">';
        $cad.='<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/graph_analytics.php?id='.$course->id.'&amp;type='.$type;
        if (isset($user) && !is_null($user))
            $cad.='&amp;user='.$user->id;
        if (isset($defaultmod) && !is_null($defaultmod))
            $cad.='&amp;mod='.$defaultmod;
        if (isset($users) && !is_null($users))
            $cad.='&amp;users='.urlencode(serialize($users));        
        $cad.='" alt="Grafico" />';
        $cad.='</div>';	
        return $cad;
    }
    
    /**
     * Devuelve el código HTML necesario para generar el tipo de gráfico 
     * especificado con las recomendaciones para el alumno del curso pasado 
     * como parámetro.
     * 
     * @global object $CFG
     * @param $course
     * @param $user
     * @param $type
     * @return string 
     */
    public function get_recommendations_graph($course,$user,$type){
        global $CFG;
        $cad='<div class="graph" style="margin: 8px auto;text-align:center">';
        $cad.='<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/graph_recommendations.php?id='.$course->id.'&amp;user='.$user->id.'&amp;type='.$type.'" alt="Grafico" />';
        $cad.='</div>';	
        return $cad;
    }
    
    /**
     * Devuelve el código HTML necesario para generar el tipo de gráfico 
     * especificado con las recomendaciones para el alumno del curso pasado 
     * como parámetro.
     * 
     * @global object $CFG
     * @param $course
     * @param $user
     * @param $type1
     * @param $type2
     * @return string 
     */
    public function get_recommendations_graphs($course,$user,$type1,$type2){
        global $CFG;
        $cad= '<div style="width:80%;text-align:center; margin: 8px auto;">';
        $cad.= '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/graph_recommendations.php?id='.$course->id.'&amp;user='.$user->id.'&amp;type='.$type1.'" alt="Grafico" align="center"/>';		
        $cad.= '<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/graph_recommendations.php?id='.$course->id.'&amp;user='.$user->id.'&amp;type='.$type2.'" alt="Grafico" align="center"/>';		
        $cad.= '</div><p style="clear:both;"></p>';
        return $cad;
    }
    
    /**
     * Muestra las pestañas de all analytics
     * @param $course
     * @return tabobject 
     */
    public function get_all_analytics_tabs($course){
        $toprow[] = new tabobject('a1', 'all_analytics.php?a=a1&amp;id='.$course->id, get_string('single_analytics','block_analytics_recommendations'));
        $toprow[] = new tabobject('a2', 'all_analytics.php?a=a2&amp;id='.$course->id, get_string('comparative_analytics','block_analytics_recommendations'));
        $toprow[] = new tabobject('a3', 'all_analytics.php?a=a3&amp;id='.$course->id, get_string('global_analytics','block_analytics_recommendations'));
        $tabs = array($toprow);
        return $tabs;
    }
    
    /**
     * Muestra las pestañas de single analytics
     * @param $user
     * @param $course
     * @return tabobject 
     */
    public function get_single_analytics_tabs($user,$course){
        $toprow[] = new tabobject('p11', 'my_progress.php?user='.$user->id.'&amp;id='.$course->id, get_string('progress','block_analytics_recommendations'));
        $toprow[] = new tabobject('a11', 'analytics1.php?user='.$user->id.'&amp;id='.$course->id, get_string('participation','block_analytics_recommendations'));
        $toprow[] = new tabobject('a12', 'analytics2.php?user='.$user->id.'&amp;id='.$course->id, get_string('stadistics1','block_analytics_recommendations'));
        $toprow[] = new tabobject('a13', 'analytics3.php?user='.$user->id.'&amp;id='.$course->id, get_string('stadistics2','block_analytics_recommendations'));
        $tabs = array($toprow);
        return $tabs;
    }
    
    /**
     * Muestra las pestañas de comparative analytics
     * @param $users
     * @param $course
     * @return tabobject 
     */
    public function get_comparative_analytics_tabs($users,$course){
        $toprow[] = new tabobject('a21', 'analytics4.php?id='.$course->id.'&users='.urlencode(serialize($users)), get_string('stadistics1','block_analytics_recommendations'));
        $toprow[] = new tabobject('a22', 'analytics5.php?id='.$course->id.'&users='.urlencode(serialize($users)), get_string('stadistics2','block_analytics_recommendations'));
        $tabs = array($toprow);
        return $tabs;
    }
    
    /**
     * Muestra las pestañas de my recommendations
     * @param $user
     * @param $course
     * @return tabobject 
     */
    public function get_my_recommendations_tabs($user,$course){
        $toprow[] = new tabobject('my_situation', 'recommendations1.php?user='.$user->id.'&amp;id='.$course->id, get_string('my_situation','block_analytics_recommendations'));
        $toprow[] = new tabobject('to_pass', 'recommendations2.php?user='.$user->id.'&amp;id='.$course->id, get_string('to_pass','block_analytics_recommendations'));
        $toprow[] = new tabobject('to_get_best_grade', 'recommendations3.php?user='.$user->id.'&amp;id='.$course->id, get_string('to_get_best_grade','block_analytics_recommendations'));
        $tabs = array($toprow);
        return $tabs;
    }
    
    /**
     * Devuelve una barra de progreso con el porcentaje pasado como parámetro.
     * @global object $CFG
     * @param $porcentaje
     * @return string 
     */
    function get_progress_bar($porcentaje) {
        global $CFG;
        
        $diferencia=100-$porcentaje;       
        if ($porcentaje>=60)
            $imagen='<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/hp_g.gif" width="'.$porcentaje.'" height="15"><img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/hp_w.gif" width="'.$diferencia.'" height="15" />';
        else if ($porcentaje>=40)
            $imagen='<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/hp_y.gif" width="'.$porcentaje.'" height="15"><img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/hp_w.gif" width="'.$diferencia.'" height="15" />';
        else
            $imagen='<img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/hp_r.gif" width="'.$porcentaje.'" height="15"><img src="'.$CFG->wwwroot.'/blocks/analytics_recommendations/pix/hp_w.gif" width="'.$diferencia.'" height="15" />';
        return $imagen;
    }	
}
?>
