App.controller('SessaoCtrl', ['$scope', 'Sessao', 'ngTableParams', '$filter', function ($scope, Sessao, ngTableParams, $filter) {
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
		$scope.setSessao = function (sessao) {
			$scope.sessao = new Sessao(sessao);
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

					params.total(orderedData.length); // set total for recalc sessaotion
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				Sessao.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioSessoes').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.sessao.salvar().$promise.then(function (data) {
					$scope.sessao = new Sessao(data);
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Sessao salva com sucesso", "success");
				});
			}
		}

		$scope.temSessao = function () {
			return $scope.sessao !== undefined;
		};
		$scope.novaSessao = function () {
			$scope.sessao = new Sessao({id: 0});
		};


		$scope.remover = function () {
			$scope.sessao.remover().$promise.then(function (data) {
				$scope.sessao = new Sessao();
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Sessao removida com sucesso", "success");
			});
		}
	}]);