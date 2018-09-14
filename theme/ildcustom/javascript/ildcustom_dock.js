function customise_dock_for_theme() {
    var dock = M.core_dock;
    //dock.cfg.position = 'top';
    //dock.cfg.orientation = 'horizontal';
	dock.set('position', 'top');
	dock.set('orientation', 'horizontal');
	//dock.on('dock:initialised', M.theme_dockmod.initialise_mod);
    //dock.on('dock:resizepanelcomplete', theme_dockmod_handle_resize);
	dock.on('dock:resizepanelcomplete', M.theme_dockmod.handle_resize);
}
 
//function theme_dockmod_handle_resize() {
/*
M.theme_dockmod.handle_resize = function() {
    var dock = M.core_dock;
	//console.log('foo ' + dock);
    var panel = dock.getPanel();
    var item = dock.getActiveItem();
    // Check its visible no point doing anything if its not.
    if (panel.visible === false || typeof(item) !== 'object') {
        return;
    }
	
    var buffer = dock.cfg.buffer;
    var screenheight = parseInt(dock.nodes.body.get('winHeight'))-(buffer*2)-dock.nodes.dock.get('offsetHeight');
    var scrolltop = panel.contentBody.get('scrollTop');
    // Reset the height of the panel so that we can accurately measure it
    panel.contentBody.setStyle('height', 'auto');
    // Remove the oversized class if it is there.
    panel.removeClass('oversized_content');
    var panelheight = panel.get('offsetHeight');
    // Set the height of the panel if required and add the oversized class
    if (panelheight > screenheight) {
        panel.contentBody.setStyle('height', (screenheight - panel.contentHeader.get('offsetHeight'))+'px');
        panel.addClass('oversized_content');
    }
    // Set the scrolltop of the panel to what it was before we started.
    if (scrolltop) {
        panel.contentBody.set('scrollTop', scrolltop);
    }
} */