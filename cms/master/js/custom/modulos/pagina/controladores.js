App.controller('PaginaCtrl', ['$scope', 'Pagina', 'ngTableParams', '$filter', 'FileUploader', 'Imagem', function ($scope, Pagina, ngTableParams, $filter, FileUploader, Imagem) {
		$scope.cfg = {
			height: 300, //set editable area's height
			focus: true, //set focus editable area after Initialize summernote
			onImageUpload: function (files, editor, welEditable) {

				function sendFile(file, editor, welEditable) {
					data = new FormData();
					data.append("file", file);
					$.ajax({
						data: data,
						type: "POST",
						url: "rest/index.php/imagem/"+$scope.pagina.album.id,
						cache: false,
						contentType: false,
						processData: false,
						success: function (imagem) {
							var img = JSON.parse(imagem);
							editor.insertImage(welEditable, img.url.replace('../../','../'));
						}
					});
				}
				sendFile(files[0], editor, welEditable);
			}
		};



		$scope.setPagina = function (pagina) {
			$scope.pagina = new Pagina(pagina);
			Imagem.load($scope.pagina.album.id).$promise.then(function (imagens) {
				$scope.images = [];
				imagens.forEach(function (img) {
					img = new Imagem(img);
					$scope.images.push(img);
				});
			});
		};
		$scope.onUpload = function () {
			$scope.setPagina($scope.pagina);
		};

		$scope.$root.excluirImagem = function (img) {
			img.remover();
			$scope.setPagina($scope.pagina);
		};

		$scope.setCapa = function (img) {
			img.setCapa();
			$scope.setPagina($scope.pagina);
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
				Pagina.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioPaginas').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.pagina.salvar().$promise.then(function (data) {
					$scope.pagina = new Pagina();
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Pagina salva com sucesso", "success");
				});
			}
		}



		$scope.temPagina = function () {
			return $scope.pagina !== undefined;
		};
		$scope.novaPagina = function () {
			$scope.pagina = new Pagina({id: 0});
		};

		$scope.enviarNovaSenha = function () {
			$scope.pagina.novaSenha();
		}


		$scope.remover = function () {
			$scope.pagina.remover().$promise.then(function (data) {
				$scope.pagina = new Pagina(data);
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Pagina removida com sucesso", "success");
			});
		}
	}])
		.controller('AcessoAlterarSenhaCtrl', ['$scope', 'Pagina', function ($scope, Pagina) {
				$scope.alterarSenha = function () {
					var parsleyForm = angular.element('#formularioAlterarSenha').parsley();
					parsleyForm.validate();
					if (parsleyForm.isValid()) {
						Pagina.alterarSenha($scope.senhaAtual, $scope.novaSenha, $scope.confirmarNovaSenha).$promise.then(function (data) {
							console.log(data);
							$scope.$root.$broadcast("addAlert", data.message, "info");
						});
					}
				}
			}]);