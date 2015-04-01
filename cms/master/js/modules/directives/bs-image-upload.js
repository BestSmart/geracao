
App.directive('bsImageUpload', function () {
	'use strict';

	return {
		restrict: 'EA',
		scope: {
			album: '=',
			onUpload: '&'
		},
		replace: true,
		templateUrl: 'app/views/bs-image-upload.html',
		controller: 'FileUploadController',
		link: function (scope, element, attribute, controller) {
		}
	};
});
App.controller('FileUploadController', ['$scope', 'FileUploader', function ($scope, FileUploader) {
		$scope.uploader = new FileUploader({
			url: 'rest/index.php/imagem/' + ($scope.album ? $scope.album : 0)
		});
		$scope.$watch('album', function () {
			$scope.uploader.url = 'rest/index.php/imagem/' + ($scope.album ? $scope.album : 0);
		});
		$scope.uploader.onCompleteAll = function () {
			$scope.onUpload()();
		};

	}]);
