// Generated by CoffeeScript 1.6.3
(function() {
  "use strict";
  var angular, dummy, resourceSelectionDirective, resourcesListDirective;

  window.ut = window.ut || {};

  ut.commons = ut.commons || {};

  angular = window.angular;

  dummy = {
    objectType: null,
    loadResource: null,
    storageHandler: null
  };

  resourcesListDirective = function() {
    return {
      restrict: "E",
      template: "<table class=\"resourceTable\">\n  <tbody>\n    <tr class=\"resourceTableHeader\">\n      <th ng-click=\"changeSorting('title')\">{{ titleLabel | i_g4i18n}}</th>\n      <th ng-click=\"changeSorting('tool')\">{{ toolLabel | i_g4i18n}}</th>\n      <th ng-click=\"changeSorting('modified')\">{{ dateLabel | i_g4i18n}}</th>\n    </tr>\n    <tr ng-repeat=\"resourceDescription in resourceDescriptions | orderBy:sort.column:sort.descending\"\n        class=\"resourceTableRow\" ng-class=\"{resourceTableRowSelected:selectedResourceId==resourceDescription.id}\"\n        ng-click=\"resourceSelected(resourceDescription.id)\" Xng-hide=\"hideContent\">\n      <td>{{resourceDescription.title}}</td>\n      <td class=\"noWrap\">{{resourceDescription.tool}}</td>\n      <td class=\"noWrap\">{{resourceDescription.modified | date:\"short\"}}</td>\n    </tr>\n  </tbody>\n</table>",
      replace: true,
      scope: true,
      link: function(scope, element, attrs) {
        return scope.hideContent = attrs["hidecontent"] === "true";
      }
    };
  };

  ut.commons.golabUtils.directive("resourceslist", [resourcesListDirective]);

  resourceSelectionDirective = function() {
    return {
      restrict: "E",
      template: "<div style=\"relative\">\n  <div class=\"resourceTableContent\">\n    <resourcesList></resourcesList>\n  </div>\n  <div class=\"resourceTableHeader\">\n    <resourcesList hideContent=\"true\"></resourcesList>\n  </div>\n  <div class=\"pleaseWaitIcon\" style=\"display: block\" ng-show=\"retrievingResourceList\"></div>\n  <div class=\"dialogButtonRow\">\n     <i class=\"fa fa-refresh fa-fw activeButton fontAweSomeButton dialogButton\" ng-click='reload()' class=\"\"></i>\n     <i class=\"fa fa-folder-open-o fa-fw activeButton fontAweSomeButton dialogButton\" ng-class=\"{disabledButton: selectedResourceId==''}\" ng-click='load()' class=\"\"></i>\n  </div>\n</div>",
      replace: true,
      link: function(scope, element, attrs) {
        var i18nBaseKey, resourceType, updateResourceList, updateResources;
        resourceType = "";
        if (attrs["resourcetype"]) {
          resourceType = attrs["resourcetype"];
        }
        i18nBaseKey = "";
        if (attrs["g4i18nbasekey"]) {
          i18nBaseKey = attrs["g4i18nbasekey"];
        }
        if (i18nBaseKey) {
          scope.titleLabel = "i_" + i18nBaseKey + ".title";
          scope.toolLabel = "i_" + i18nBaseKey + ".tool";
          scope.dateLabel = "i_" + i18nBaseKey + ".date";
        } else {
          scope.titleLabel = "title";
          scope.toolLabel = "tool";
          scope.dateLabel = "date";
        }
        scope.elem = element;
        scope.retrievingResourceList = false;
        scope.resourceDescriptions = [];
        scope.selectedResourceId = "";
        updateResourceList = function(metadatas) {
          var filterResourceType, id, onlyMetadata;
          filterResourceType = function(metadata) {
            if (resourceType) {
              return metadata.target.objectType === resourceType;
            } else {
              return true;
            }
          };
          scope.resourceDescriptions = (function() {
            var _results;
            _results = [];
            for (id in metadatas) {
              onlyMetadata = metadatas[id];
              if (filterResourceType(onlyMetadata.metadata)) {
                _results.push(scope.storageHandler.getResourceDescription(onlyMetadata));
              }
            }
            return _results;
          })();
          scope.retrievingResourceList = false;
          return scope.$apply();
        };
        updateResources = function() {
          scope.retrievingResourceList = true;
          return scope.storageHandler.listResourceMetaDatas(function(error, onlyMetadatas) {
            if (error) {
              alert("Problems with getting the list of resources:\n" + error);
              return scope.retrievingResourceList = false;
            } else {
              return updateResourceList(onlyMetadatas);
            }
          });
        };
        scope.$watch("dialogBoxChangeCounter", function() {
          if (element.is(':visible')) {
            return updateResources();
          }
        });
        scope.reload = function() {
          return updateResources();
        };
        scope.resourceSelected = function(id) {};
        scope.load = function() {
          if (scope.selectedResourceId && scope.loadResource) {
            return scope.storageHandler.readResource(scope.selectedResourceId, function(error, resource) {
              if (error) {
                return alert("Problems with reading the resource, with id " + scope.selectedResourceId + ":\n" + error);
              } else {
                if (resource) {
                  return scope.loadResource(resource);
                }
              }
            });
          }
        };
        return scope.resourceSelected = function(id) {
          return scope.selectedResourceId = id;
        };
      }
    };
  };

  ut.commons.golabUtils.directive("resourceselection", [resourceSelectionDirective]);

}).call(this);

/*
//@ sourceMappingURL=resourceIOUI.map
*/
