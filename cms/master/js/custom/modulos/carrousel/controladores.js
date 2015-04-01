App.controller('CarrouselCtrl', ['$scope', 'editableOptions', 'editableThemes', 'Carrousel', 'Slide', 'ngTableParams', '$filter', '$modal', '$http', 'Imagem', function ($scope, editableOptions, editableThemes, Carrousel, Slide, ngTableParams, $filter, $modal, $http, Imagem) {

		editableOptions.theme = 'bs3';

		editableThemes.bs3.inputClass = 'input-sm';
		editableThemes.bs3.buttonsClass = 'btn-sm';
		editableThemes.bs3.submitTpl = '<button type="submit" class="btn btn-success"><span class="fa fa-check"></span></button>';
		editableThemes.bs3.cancelTpl = '<button type="button" class="btn btn-default" ng-click="$form.$cancel()">' +
				'<span class="fa fa-times text-muted"></span>' +
				'</button>';

		$scope.cfg = {
			height: 150, //set editable area's height
			focus: true    //set focus editable area after Initialize summernote
		};

		Carrousel.load().$promise.then(function (data) {
			$scope.setCarrousel(data);
		});

		$scope.addSlide = function () {
			$scope.slide = new Slide();
			$scope.carrousel.slides.push($scope.slide);
			$scope.tableParams.reload();
		};
		$scope.removerSlide = function () {
			var index = $scope.carrousel.slides.indexOf($scope.slide);
			if (index >= 0) {
				$scope.carrousel.slides.splice(index, 1);
			}
		};
		$scope.setCarrousel = function (carrousel) {
			$scope.carrousel = new Carrousel(carrousel);
			$scope.tableParams.reload();
		};

		$scope.setSlide = function (slide) {
			var index = $scope.carrousel.slides.indexOf(slide);
			if (index >= 0) {
				$scope.slide = $scope.carrousel.slides[index];
			}
		}
		$scope.temSlide = function () {
			return $scope.slide !== undefined;
		}
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioCarrousels').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.carrousel.salvar().$promise.then(function (data) {
					Carrousel.load().$promise.then(function (data) {
						Carrousel.load().$promise.then(function (data) {
							$scope.setCarrousel(data);
							$scope.slide = $scope.carrousel.slides[0];
						});

						$scope.tableParams.reload();
					});
					$scope.$root.$broadcast("addAlert", "Carrosel salvo com sucesso", "success");
				});
			}
		}

		$scope.temCarrousel = function () {
			return $scope.carrousel !== undefined;
		};
		$scope.tableParams = new ngTableParams({
			page: 1, // show first page
			count: 10, // count per page
			sorting: {
				'titulo': 'asc'
			}
		}, {
			getData: function ($defer, params) {
				var loadData = function (slides) {
					// use build-in angular filter
					var filteredData = params.filter() ?
							$filter('filter')(slides, params.filter()) :
							data;
					var orderedData = params.sorting() ?
							$filter('orderBy')(filteredData, params.orderBy()) :
							data;

					params.total(orderedData.length); // set total for recalc produtotion
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				if ($scope.temCarrousel()) {
					loadData($scope.carrousel.slides);
				}
			}
		});

	}]);