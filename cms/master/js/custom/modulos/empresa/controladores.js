App.controller('EmpresaCtrl', ['$scope', 'editableOptions', 'editableThemes', 'Empresa', 'Detalhe', 'ngTableParams', '$filter', '$modal', '$http', 'Imagem', function ($scope, editableOptions, editableThemes, Empresa, Detalhe, ngTableParams, $filter, $modal, $http, Imagem) {

		editableOptions.theme = 'bs3';

		editableThemes.bs3.inputClass = 'input-sm';
		editableThemes.bs3.buttonsClass = 'btn-sm';
		editableThemes.bs3.submitTpl = '<button type="submit" class="btn btn-success"><span class="fa fa-check"></span></button>';
		editableThemes.bs3.cancelTpl = '<button type="button" class="btn btn-default" ng-click="$form.$cancel()">' +
				'<span class="fa fa-times text-muted"></span>' +
				'</button>';

		$scope.cfg = {
			height: 150, //set editable area's height
			focus: true, //set focus editable area after Initialize summernote
			onImageUpload: function (files, editor, welEditable) {

				function sendFile(file, editor, welEditable) {
					data = new FormData();
					data.append("file", file);
					$.ajax({
						data: data,
						type: "POST",
						url: "rest/index.php/imagem/" + $scope.empresa.album.id,
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

		Empresa.load().$promise.then(function (data) {
			$scope.setEmpresa(data);
		});

		$scope.addDetalhe = function () {
			$scope.empresa.detalhes.push(new Detalhe());
		};
		$scope.removerDetalhe = function (detalhe) {
			var index = $scope.empresa.detalhes.indexOf(detalhe);
			if (index >= 0) {
				$scope.empresa.detalhes.splice(index, 1);
			}
		};
		$scope.afterUpload = function () {
			$scope.setEmpresa($scope.empresa);
		};
		$scope.setEmpresa = function (empresa) {
			$scope.empresa = new Empresa(empresa);
			Imagem.load($scope.empresa.album.id).$promise.then(function (imagens) {
				$scope.images = [];
				imagens.forEach(function (img) {
					img = new Imagem(img);
					$scope.images.push(img);
				});
			});
		};
		$scope.excluirImagem = function (img) {
			img.remover();
			$scope.setEmpresa($scope.empresa);
		};


		$scope.setCapa = function (img) {
			img.setCapa();
			$scope.setEmpresa($scope.empresa);
		}

		$scope.salvar = function () {
			var parsleyForm = angular.element('#formularioEmpresas').parsley();
			parsleyForm.validate();
			if (parsleyForm.isValid()) {
				$scope.empresa.salvar().$promise.then(function (data) {
					$scope.setEmpresa(new Empresa(data));
					$scope.$root.$broadcast("addAlert", "Empresa salva com sucesso", "success");
				});
			}
		}

		$scope.temEmpresa = function () {
			return $scope.empresa !== undefined;
		};

	}]);