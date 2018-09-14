// Generated by CoffeeScript 1.6.3
(function() {
  "use strict";
  window.ut = window.ut || {};

  ut.commons = ut.commons || {};

  ut.commons.golabUtils.controller("testGolabContainer", function($scope, browser) {});

  $(function() {
    var canvasDragTest, canvasDragTestContext, canvasTest, canvasTestContext;
    canvasTest = $("#canvasTest");
    canvasTestContext = canvasTest[0].getContext('2d');
    canvasTestContext.fillStyle = '#FF0000';
    canvasTestContext.fillRect(0, 0, 200, 100);
    canvasDragTest = $("#canvasDragTest");
    canvasDragTestContext = canvasDragTest[0].getContext('2d');
    canvasDragTestContext.fillStyle = '#00FF00';
    canvasDragTestContext.fillRect(0, 0, 200, 100);
    return canvasDragTest.draggable();
  });

}).call(this);

/*
//@ sourceMappingURL=golabContainer.map
*/
