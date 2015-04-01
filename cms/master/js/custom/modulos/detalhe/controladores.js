App.controller('DetalheCtrl', ['$scope', 'Detalhe', 'ngTableParams', '$filter', function ($scope, Detalhe, ngTableParams, $filter) {
		$scope.cfg = {
			height: 300, //set editable area's height
			focus: true,
			// toolbar
			toolbar: [
				['edit', ['undo', 'redo']],
				['headline', ['style']],
				['style', ['bold', 'italic', 'underline', 'superscript', 'subscript', 'strikethrough', 'clear']],
				['textsize', ['fontsize']],
				['fontclr', ['color']],
				['alignment', ['ul', 'ol', 'paragraph', 'lineheight']],
				['view', ['fullscreen','codeview']],
				['help', ['help']]
			]
		};
		$scope.setDetalhe = function (detalhe) {
			$scope.detalhe = new Detalhe(detalhe);
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

					params.total(orderedData.length); // set total for recalc detalhetion
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				Detalhe.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioDetalhes').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.detalhe.salvar().$promise.then(function (data) {
					$scope.detalhe = new Detalhe(data);
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Detalhe salvo com sucesso", "success");
				});
			}
		}

		$scope.temDetalhe = function () {
			return $scope.detalhe !== undefined;
		};
		$scope.novoDetalhe = function () {
			$scope.detalhe = new Detalhe({id: 0});
		};


		$scope.remover = function () {
			$scope.detalhe.remover().$promise.then(function (data) {
				$scope.detalhe = new Detalhe();
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Detalhe removido com sucesso", "success");
			});
		}
	}]);