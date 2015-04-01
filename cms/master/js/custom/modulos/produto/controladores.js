App.controller('ProdutoCtrl', ['$scope', 'editableOptions', 'editableThemes', 'Produto', 'Detalhe', 'ngTableParams', '$filter', '$modal', '$http', 'Imagem', function ($scope, editableOptions, editableThemes, Produto, Detalhe, ngTableParams, $filter, $modal, $http, Imagem) {

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
			],
			onImageUpload: function (files, editor, welEditable) {

				function sendFile(file, editor, welEditable) {
					data = new FormData();
					data.append("file", file);
					$.ajax({
						data: data,
						type: "POST",
						url: "rest/index.php/imagem/" + $scope.produto.album.id,
						cache: false,
						contentType: false,
						processData: false,
						success: function (imagem) {
							var img = JSON.parse(imagem);
							editor.insertImage(welEditable, img.url.replace('../../', '../'));
						}
					});
				}
				sendFile(files[0], editor, welEditable);
			}
		};
		$scope.addDetalhe = function () {
			$scope.produto.detalhes.push(new Detalhe());
		};
		$scope.removerDetalhe = function (detalhe) {
			var index = $scope.produto.detalhes.indexOf(detalhe);
			if (index >= 0) {
				$scope.produto.detalhes.splice(index, 1);
			}
		};
		$scope.afterUpload = function () {
			$scope.setProduto($scope.produto);
		};
		$scope.setProduto = function (produto) {
			$scope.produto = new Produto(produto);
			Imagem.load($scope.produto.album.id).$promise.then(function (imagens) {
				$scope.images = [];
				imagens.forEach(function (img) {
					img = new Imagem(img);
					$scope.images.push(img);
				});
			});
		};

		$scope.$root.excluirImagem = function (img) {
			img.remover();
			$scope.setProduto($scope.produto);
		};

		$scope.setCapa = function (img) {
			img.setCapa();
			$scope.setProduto($scope.produto);
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

					params.total(orderedData.length); // set total for recalc produtotion
					$defer.resolve(orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count()));
				};
				Produto.load().$promise.then(loadData);
			}
		});
		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioProdutos').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.produto.salvar().$promise.then(function (data) {
					$scope.produto = new Produto(data);
					$scope.tableParams.reload();
					$scope.$root.$broadcast("addAlert", "Produto salvo com sucesso", "success");
				});
			}
		}

		$scope.temProduto = function () {
			return $scope.produto !== undefined;
		};
		$scope.novoProduto = function () {
			$scope.produto = new Produto({id: 0});
		};
		$scope.remover = function () {
			$scope.produto.remover().$promise.then(function (data) {
				$scope.produto = new Produto();
				$scope.tableParams.reload();
				$scope.$root.$broadcast("addAlert", "Produto removido com sucesso", "success");
			});
		};

	}]);