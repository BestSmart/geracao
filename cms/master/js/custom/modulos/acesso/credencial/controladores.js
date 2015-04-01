App.controller('CredencialCtrl', ['$scope', 'Credencial', 'ngTableParams', '$filter', function ($scope, Credencial, ngTableParams, $filter) {


		$scope.setCredencial = function (credencial) {
			$scope.credencial = new Credencial(credencial);
		};
		$scope.tableParams = new ngTableParams({
			page: 1, // show first page
			count: 10, // count per page
			sorting: {
				'email': 'asc'
			}
		}, {
			getData: function ($defer, params) {
				var loadData = function (credenciais) {
					// use build-in angular filter
					var filteredData = params.filter() ?
							$filter('filter')(credenciais, params.filter()) :
							data;
					var orderedData = params.sorting() ?
							$filter('orderBy')(filteredData, params.orderBy()) :
							data;

					params.total(orderedData.length); // set total for recalc pagination
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				Credencial.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioCredenciais').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.credencial.salvar().$promise.then(function (data) {
					$scope.credencial = new Credencial(data);
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Credencial salvo com sucesso", "success");
				});
			}
		}

		$scope.temCredencial = function () {
			return $scope.credencial !== undefined;
		};
		$scope.novaCredencial = function () {
			$scope.credencial = new Credencial({id: 0});
		};

		$scope.enviarNovaSenha = function () {
			$scope.credencial.novaSenha();
		}


		$scope.remover = function () {
			$scope.credencial.remover().$promise.then(function (data) {
				$scope.credencial = new Credencial(data);
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Credencial removida com sucesso", "success");
			});
		}
	}])
		.controller('AlterarSenhaCtrl', ['$scope', 'Credencial', function ($scope, Credencial) {
				$scope.alterarSenha = function () {
					var parsleyForm = angular.element('#formularioAlterarSenha').parsley();
					parsleyForm.validate();
					if (parsleyForm.isValid()) {
						Credencial.alterarSenha($scope.senhaAtual, $scope.novaSenha, $scope.confirmarNovaSenha).$promise.then(function (data) {
							$scope.$root.$broadcast("addAlert", 'Senha Alterada com Sucesso!', "info");
						});
					}
				}
			}]);