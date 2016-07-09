angular.module('OpenSPG', [
        'ui.bootstrap',
        'angularFileUpload'
    ])
    .controller('BaseCtrl', ['$rootScope', BaseCtrl])
    .controller('MediaController', ['$rootScope', 'FileUploader', MediaController])
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
	});
;

function BaseCtrl($rootScope) {
    $rootScope.spinner = false;
}

function MediaController($scope, FileUploader) {
    this.data = {};
    $scope.uploader = new FileUploader();
    $scope.uploader.url = Routing.generate('media_media_upload');
    $scope.uploader.alias = 'media[mediaFile]';
    $scope.uploader.autoUpload = true;
    $scope.uploader.formData = {
    	media: {
    		parent: this.data.parent
    	}
    };
    console.info($scope.uploader.formData);
    $scope.uploader.onCompleteAll = function(){
    	$scope.tab = 'files';
    };
}