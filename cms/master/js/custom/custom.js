
var config;

App.run(['$rootScope', 'SessionService', '$state', '$http', 'Config', function ($rootScope, SessionService, $state, $http, Config) {
		Config.setConfig(config);

		$rootScope.checkOperacao = function (operacao) {
			var equals = false;
			var authorities = SessionService.getUser().authorities;
			authorities.some(function (auth) {
				var indexOf = operacao.indexOf(auth.authority);
				equals = indexOf !== -1;
				return equals;
			});
			return equals;
		};
		$http.get('server/config.json').success(function (data) {
			config.params = data;
		});
		$http.get('rest/index.php/credencial').success(function (data) {
			SessionService.setUser(data);
			$http.get('server/sidebar-menu.json').error(function () {
				$state.go('login');
			});
		});

		$rootScope.$on('event:auth-loginRequired', function () {
			$state.go('login');
		});
		$rootScope.$on('event:auth-loginConfirmed', function (event, data) {
			$state.go('principal');
		});
	}])
		.config(['$stateProvider', '$urlRouterProvider', '$httpProvider',
			function ($stateProvider, $urlRouterProvider, $httpProvider) {
				config = {
					urlRouterProvider: $urlRouterProvider,
					stateProvider: $stateProvider
				};
				$httpProvider.interceptors.push('responseObserver');
				$httpProvider.interceptors.push('httpLoadingInterceptor');
			}])
		.factory('responseObserver',
				['$q', '$rootScope', function ($q, $rootScope) {
						return function (promise) {
							return promise.then(function (successResponse) {
								$rootScope.$broadcast("httpSuccess", "Operação realizada com sucesso: ");
								return successResponse;
							}, function (errorResponse) {
								$rootScope.$broadcast("httpError", errorResponse.data)
								return $q.reject(errorResponse);
							});
						};
					}])
		.factory('httpLoadingInterceptor', ['$q', '$rootScope', '$log', function ($q, $rootScope, $log) {
				var numLoadings = 0;
				return {
					'request': function (config) {
						numLoadings++;
						$rootScope.$broadcast("loader_show");
						return config || $q.when(config)
					},
					'response': function (response) {
						if ((--numLoadings) === 0) {
							$rootScope.$broadcast("loader_hide");
						}
						return response || $q.when(response);
					},
					'responseError': function (response) {
						if (!(--numLoadings)) {
							// Hide loader
							$rootScope.$broadcast("loader_hide");
						}
						return $q.reject(response);
					}
				};
			}])
		.factory('Config', [function () {
				var stateProvider, urlRouterProvider;
				return {
					'stateProvider': stateProvider,
					'urlRouterProvider': urlRouterProvider,
					setConfig: function (config) {
						this.stateProvider = config.stateProvider;
						this.urlRouterProvider = config.urlRouterProvider;
					}
				};
			}])
		.directive("loader", [function () {
				return function ($scope, element, attrs) {
					$scope.$on("loader_show", function () {
						return element.addClass("whirl sphere");
					});
					return $scope.$on("loader_hide", function () {
						return element.removeClass("whirl sphere");
					});
				};
			}])
		.directive("gallery", ['$timeout', function ($timeout) {
				return {
					restrict: 'EA',
					priority: -1000,
					scope: {
						images: '=',
						excluirImagem :'&',
						setCapa :'&'
					},
					templateUrl: 'app/views/gallery.html',
					link: function (scope, elem, attr) {
						scope.excluir = function(img){
							scope.excluirImagem()(img);
						};
						scope.capa = function(img){
							scope.setCapa()(img);
						};
						var gallery;
						scope.$watch('images', function () {
							if (scope.images) {
								$timeout(function () {
									gallery = $(elem).lightGallery({
										mode: 'fade',
										speed: 500,
										selector: '.grid-item'
									});
								}, 500);
							}

						});
					}
				};
			}]);