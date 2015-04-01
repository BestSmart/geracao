App.controller('PrincipalCtrl', [
		'$scope',
		'SessionService',
		'$resource',
		'LoginService',
		'$window',
		'Config',
		'$timeout',
		'$state',
		'toaster',
		function($scope, SessionService, $resource,
				LoginService, $window, Config, $timeout, $state, toaster ) {
			SessionService.reloadUser();
			$scope.user = SessionService.getUser();
			$scope.logout = function(){
				LoginService.logout();
			};
			
			$scope.logout = function() {
				LoginService.logout(function(success, data) {
					if (!success) {
						console.log(data);
					}
					$window.location.reload();
				});
			};
			
			$scope.alerts = [];
			$scope.closeAlert = function(index) {
			    $scope.alerts.splice(index, 1);
			 };
			 $scope.alertsTimeout = [];
			var addAlert = function(data, _type){
				toaster.pop(_type, "", data);
			}
			
			$scope.keepAlert = function(index){
				$timeout.cancel($scope.alertsTimeout[index]);
			}
			$scope.$on("httpError", function(event, data){
				addAlert(data.message, "error");
			});
			
			$scope.$on("addAlert", function(event, data, type){
				addAlert(data,type);
			});
		} ]);