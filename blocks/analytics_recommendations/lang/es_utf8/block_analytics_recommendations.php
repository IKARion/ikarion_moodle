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

$string['pluginname'] = 'Análisis y Recomendaciones';
$string['modulename']='Análisis y Recomendaciones';
$string['modulenameplural']='Análisis y Recomendaciones';

$string['participation1']='Análisis por secciones/actividades';
$string['stadistics1']='Análisis por secciones/actividad';
$string['stadistics2']='Análisis por actividades';
$string['stadistics3']='Análisis de participación 3';
$string['analytics']='Análisis';
$string['recommendations']='Recomendaciones';
$string['progress']='Progreso';
$string['my_participation']='Mi participación';
$string['all_analytics']='Todos los análisis';
$string['all_recommendations']='Todas las recomendaciones';
$string['analytics_recommendations'] = 'Análisis y recomendaciones';
$string['my_analytics'] = 'Mis análisis';
$string['my_recommendations'] = 'Mis recomendaciones';
$string['my_progress'] = 'Mi progreso';

$string['week']='Semana';
$string['weeks']='Semanas';
$string['topic']='Tema';
$string['topics']='Temas';
$string['section']='Sección';
$string['sections']='Secciónes';
$string['activity']='Actividad';

$string['choose_student']='Elige alumno/a';
$string['choose_students']='Elige alumnos/as';
$string['choose_activity']='Elige actividad';

$string['participation_average'] = 'Participación media';
$string['participation_all_activities'] = 'Participación en todas las actividades';
$string['participation_in'] = 'Participación en ';
$string['average_participation_all_activities'] = 'Participación media en todas las actividades';

$string['analytics_recommendations:init']='iniciar el seguimiento de un curso';
$string['analytics_recommendations:viewglobal']='ver análisis y/o recomendaciones de todos los estudiantes';
$string['analytics_recommendations:viewsingle']='ver análisis y/o recomendaciones de un estudiante';

$string['student'] = 'Alumno';
$string['students'] = 'Alumnos';
$string['teacher'] = 'Profesor';
$string['summaries'] = 'Resumenes';

$string['initiate_follow-up'] = 'Iniciar seguimiento';
$string['follow-up_ok'] = 'Seguimiento iniciado correctamente. Desde la página principal del curso podrás consultar los análisis de participación del alumnado y las recomendaciones del sistema (si se ha elegido un curso de referencia).';
$string['reference_course'] = 'Curso de referencia';
$string['my_current_situation'] = 'Mi situación actual';
$string['your_participation'] = 'Tu participación';
$string['activities'] = 'Actividades';
$string['percentage'] = 'Porcentaje';
$string['need_to_pass'] = '&iquest;Que tienes que hacer para aprobar?';
$string['participation_to_pass'] = 'Participación para aprobar';
$string['best_grade'] = 'Si quieres conseguir la mejor calificación ...';
$string['best_participation'] = 'La participación del mejor estudiante';
$string['none']='Ninguno';
$string['my_situation'] = 'Mi situación';
$string['to_pass'] = 'Para aprobar';
$string['to_get_best_grade'] = 'Para conseguir la mejor calificación';
$string['grade'] = 'Calificación';
$string['avg'] = 'Media';
$string['avg_grade'] = 'Calificación media';
$string['final_avg_grade'] = 'Calificación media final';
$string['participation'] = 'Participación';
$string['expected_grade'] = 'Calificación estimada';
$string['expected_grades'] = 'Calificaciones estimadas';
$string['effort']='Esfuerzo';

$string['single_analytics']='Análisis individual';
$string['comparative_analytics']='Análisis comparativo';
$string['global_analytics']='Análisis global';

$string['high_participation']='Participación alta';
$string['half_participation']='Participación media';
$string['low_participation']='Participación baja';

$string['show']='Mostrar';
$string['sortasc']='Ordenar (ascendente)';
$string['sortdesc']='Ordenar (descendente)';

$string['analytics1_message']='Esta tabla muestra tu participación en cada una las actividades propuestas para cada sección del curso.';
$string['analytics2_message']='Esta gráfica muestra tu participación  y la participación media de todos los alumnos del curso en un determinado tipo de actividad.';
$string['analytics3_message']='Esta gráfica muestra tu participación media en cada tipo de actividad comparada con la media de participación del curso.';
$string['analytics4_message']='Esta gráfica muestra la participación de los estudiantes elegidos para cada actividad propuesta en el curso.';
$string['analytics5_message']='Esta gráfica muestra la participación de los estudiantes elegicos en los distintos tipos de actividades propuestas en el curso.';
$string['all_analytics_message']='Esta tabla muestra un resumen de la participación de todos los estudiantes del curso en cada tipo de actividad propuesta. También muestra la participación media por alumno y por actividad. Todas las columnas pueden ordenar ascendente y descendentemente.';	

$string['recommendations1_message']='Esta gráfica muestra tu participación en cada tipo de atividad propuesta en el curso. Además muestra una estimación de tu calificación final.';
$string['recommendations2_message']='La gráfica de la izquierda muestra tu participación en cada tipo de atividad propuesta en el curso comparada con la participación estimada necesaria para superar el curso.<br/>La gráfica de la derecha muestra el esfuerzo estimado necesario en cada actividad para superar el curso con éxito.';
$string['recommendations3_message']='La gráfica de la izquierda muestra tu participación en cada tipo de atividad propuesta en el curso comparanda con la participación estimada necesaria para superar el curso con la mejor calificación.<br/>La gráfica de la derecha muestra el esfuerzo estimado necesario en cada actividad para superar el curso con la mejor calificación.';
$string['all_recommendations_message']='Esta tabla muestra una estimación de la calificación final de los estudiantes en función de su participación actual en las distintas actividades propuestas en el curso.';
$string['my_progress_message']='Esta tabla muestra tu participación en cada tipo de actividad propuesta en cada sección del curso. Además, para cada sección que tiene actividades de evaluación, muestra la media de la calificaciones obtenidas.';

$string['chooseuser_form'] = 'Estudiante'; 
$string['chooseuser_form_help'] = 'Eligue a un estudiante para ver detalles de su participación.';

$string['chooseusers_form'] = 'Estudiantes'; 
$string['chooseusers_form_help'] = 'Elige a varios estudiantes para ver un análisis comparativo de su participación en el curso.';

$string['defaultmod_form'] = 'Actividad'; 
$string['defaultmod_form_help'] = 'Elige una actividad para ver un análisis detallado de participación.';

$string['setup_form'] = 'Curso de referencia'; 
$string['setup_form_help'] = 'Elige un curso de referencia para poder generar recomendaciones para los estudiantes de este curso. Sólo estarán disponibles los cursos en los que previamente se haya instalado el bloque Análisis y Recomendaciones.';

$string['show_recommendations']='Mostrar recomendaciones';
$string['show_recommendations_help'] = 'Elige si deseas que los estudiantes puedan ver recomendaciones o no.';

$string['sort_asc']='Orden ascendente';
$string['sort_desc']='Orden descendente';
?>
