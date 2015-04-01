App.controller('AlbumCtrl', ['$scope', 'Album', 'ngTableParams', '$filter', '$modal', '$http', 'Imagem', function ($scope, Album, ngTableParams, $filter, $modal, $http, Imagem) {
		$scope.afterUpload = function(){
			$scope.setAlbum($scope.album);
		}
		$scope.setAlbum = function (album) {
			$scope.album = new Album(album);
			Imagem.load($scope.album.id).$promise.then(function (imagens) {
				$scope.images = [];
				imagens.forEach(function (img) {
					img = new Imagem(img);
					$scope.images.push(img);
				});
			});
		};

		$scope.$root.excluirImagem = function (img) {
			img.remover();
			$scope.setAlbum($scope.album);
		};

		$scope.setCapa = function (img) {
			img.setCapa();
			$scope.setAlbum($scope.album);
		}
		$scope.tableParams = new ngTableParams({
			page: 1, // show first page
			count: 10, // count per page
			sorting: {
				'email': 'asc'
			}
		}, {
			getData: function ($defer, params) {
				var loadData = function (albuns) {
					// use build-in angular filter
					var filteredData = params.filter() ?
							$filter('filter')(albuns, params.filter()) :
							data;
					var orderedData = params.sorting() ?
							$filter('orderBy')(filteredData, params.orderBy()) :
							data;

					params.total(orderedData.length); // set total for recalc albumtion
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				Album.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioAlbuns').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.album.salvar().$promise.then(function (data) {
					$scope.album = new Album(data);
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Album salvo com sucesso", "success");
				});
			}
		};

		$scope.temAlbum = function () {
			return $scope.album !== undefined;
		};
		$scope.novoAlbum = function () {
			$scope.album = new Album({id: 0});
		};
		$scope.remover = function () {
			$scope.album.remover().$promise.then(function (data) {
				$scope.album = new Album();
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Album removido com sucesso", "success");
			});
		};

	}]);