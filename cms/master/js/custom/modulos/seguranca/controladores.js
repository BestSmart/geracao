App
.controller('LoginCtrl',[ '$scope', 'LoginService','$animate','$state', '$timeout','ngDialog','$window',function($scope, LoginService, $animate, $state, $timeout, ngDialog, $window) {
	$scope.credencial = {};
	$scope.toastPosition = {
			bottom: false,
			top: true,
			left: false,
			right: true
	};
	$scope.alert = {close:true};
	$scope.closeAlert = function() {
	    $scope.alert.close = true;
	 };
	 
	var addAlert = function(data, _type){
		$scope.alert = { type: _type, msg: data };
		$timeout.cancel($scope.currentTimeout);
		$scope.currentTimeout = $timeout(function() {
			$scope.closeAlert();
		}, 10000); 
	}
	$scope.login = function(){
		LoginService.login($scope.credencial, function(success, data){
			if(success){
				$state.go('principal');
			}else{
				addAlert(data.message, "error");
			}
		});
	}
	
	$scope.recuperarSenha = function(){
		LoginService.recuperarSenha($scope.credencial, function(success, data){
			addAlert(data.message, "success");
			$timeout(function() {
				ngDialog.close();
			}, 5000); 
		});
	}
} ]);