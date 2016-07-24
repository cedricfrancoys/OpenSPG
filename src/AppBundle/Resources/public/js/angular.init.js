angular.module('OpenSPG', [
        'ui.bootstrap',
        'angularFileUpload'
    ])
    .controller('BaseCtrl', ['$rootScope', BaseCtrl])
    .directive('input', function ($parse) {
	  return {
	    restrict: 'E',
	    require: '?ngModel',
	    link: function (scope, element, attrs) {
	      if (attrs.ngModel && attrs.value) {
	        $parse(attrs.ngModel).assign(scope, attrs.value);
	      }
	    }
	  };
	})
    .controller('MediaController', ['$rootScope', 'FileUploader', MediaController])
    ;
;

function BaseCtrl($rootScope) {
    $rootScope.spinner = false;
}

function MediaController($scope, FileUploader) {
    this.data = {};
    this.tab = 'files';
    $scope.uploader = new FileUploader();
    $scope.uploader.url = Routing.generate('media_media_upload');
    $scope.uploader.alias = 'media';
    $scope.uploader.autoUpload = true;
    var $this = this;
    $scope.uploader.onAfterAddingFile = function(item){
    	item.formData = {
	    	parent: $this.data.parent
	    };
    }
    $scope.uploader.onCompleteAll = function(){
    	$this.tab = 'files';
    };
}