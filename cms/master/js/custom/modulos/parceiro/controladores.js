App.controller('ParceiroCtrl', ['$scope', 'editableOptions', 'editableThemes', 'Parceiro', 'Detalhe', 'ngTableParams', '$filter', '$modal', '$http', 'Imagem', function ($scope, editableOptions, editableThemes, Parceiro, Detalhe, ngTableParams, $filter, $modal, $http, Imagem) {

		editableOptions.theme = 'bs3';

		editableThemes.bs3.inputClass = 'input-sm';
		editableThemes.bs3.buttonsClass = 'btn-sm';
		editableThemes.bs3.submitTpl = '<button type="submit" class="btn btn-success"><span class="fa fa-check"></span></button>';
		editableThemes.bs3.cancelTpl = '<button type="button" class="btn btn-default" ng-click="$form.$cancel()">' +
				'<span class="fa fa-times text-muted"></span>' +
				'</button>';

		$scope.cfg = {
			height: 300, //set editable area's height
			focus: true,
			// toolbar
			toolbar: [
				['font', ['bold', 'italic', 'underline', 'clear']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['height', ['height']],
				['table', ['table']],
				['insert', ['link']],
				['view', ['fullscreen']],
				['help', ['help']]
			]
		};
		$scope.addDetalhe = function () {
			$scope.parceiro.detalhes.push(new Detalhe());
		}
		$scope.removerDetalhe = function (detalhe) {
			var index = $scope.parceiro.detalhes.indexOf(detalhe);
			if (index >= 0) {
				$scope.parceiro.detalhes.splice(index, 1);
			}
		}
		$scope.afterUpload = function () {
			$scope.setParceiro($scope.parceiro);
		}
		$scope.setParceiro = function (parceiro) {
			$scope.parceiro = new Parceiro(parceiro);
			Imagem.load($scope.parceiro.album.id).$promise.then(function (imagens) {
				$scope.images = [];
				imagens.forEach(function (img) {
					img = new Imagem(img);
					$scope.images.push(img);
				});
			});
		};

		$scope.$root.excluirImagem = function (img) {
			img.remover();
			$scope.setParceiro($scope.parceiro);
		};

		$scope.setCapa = function (img) {
			img.setCapa();
			$scope.setParceiro($scope.parceiro);
		}
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

					params.total(orderedData.length); // set total for recalc parceirotion
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				Parceiro.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioParceiros').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.parceiro.salvar().$promise.then(function (data) {
					$scope.parceiro = new Parceiro(data);
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Parceiro salvo com sucesso", "success");
				});
			}
		}

		$scope.temParceiro = function () {
			return $scope.parceiro !== undefined;
		};
		$scope.novoParceiro = function () {
			$scope.parceiro = new Parceiro({id: 0});
		};
		$scope.remover = function () {
			$scope.parceiro.remover().$promise.then(function (data) {
				$scope.parceiro = new Parceiro();
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Parceiro removido com sucesso", "success");
			});
		};

	}]);