Documentation of Changes made to moodle to support the specific needs of ikarion.


folder: /group

Added files for task management:

    group_task_edit.php
    group_task_edit_form.php
    group_task_mapping.php
    group_task_mapping_edit.php
    group_task_mapping_form.php
    group_task_select.php
    group_task_select_form.php
		
The files contain the necessary code to modify the group creation and management to
be able to create a mapping from groups to tasks and create/delete tasks.

file: index.php:

Modified this to have links to the task editing pages.
Changes are marked with comments containing the word "ikarion"

files: autogroup.php, autogroup_form.php

Code added for the groupal plugin, to generate groups in a smart way.
There is also form code and db acess code here for defining a task that the created groups are going to be connected to.
	
file: groupal.php

Implementation of the groupal algorithms.

folder: /blocks/groupassign/
	
This is a dummy block plugin to create the database tables that are necessary for saving tasks and their mapping to groups
to the databse. 
The tables are defined in db/install.xml and are created when the plugin is installed.
	

file: /lang/en/groups

Added strings for various html elemnts as labels. Go here if you want to add support for a specific language


files: /mod/wiki/view.php, /mod/wiki/create.php, lib/grouplib.php

Made changes to automatically select a certain wiki for groupmembers if the wiki is in the list of relevant resources
for the task that the group is assigned.
Relevant parts are marked with a comment containing the word "ikarion"
Specifically a javascript script (code as string in php file) 
was used to remove the selection option if an appropriate group was found.

in the grouplib file we specifically added the function groups_get_user_group_for_module($userid, $moduleid)
to see if there is a group connected to the wiki via being in the relevant resources of a task.
	

file: mod/forum/view.php
	
Added functionality so the appropriate forum is opened based on the forum showing up in a resource list of
a task that is connected to a group that the user is in.
	
folder: /admin/tool/log/store/xapi/lib

We made changes to the xapi logging to add group and task data to the xapi statement.
There are 3 folders - emitter, expander, translator which all mirorr the same strucuture
having a folder /src/events where to emit different moodle events with the base event being in event.php

The main changes are in the file /expander/src/Events/Event.php where we added code to fetch group and task
data from the db and add it to the xapi statement.














