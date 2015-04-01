App
		.factory('Empresa', ['$resource', 'Detalhe', function ($resource, Detalhe) {
				var Server = $resource('rest/index.php/empresa/')
				function Empresa(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.nome = obj.nome;
						this.album = obj.album;
						this.historia = obj.historia;
						this.slogan = obj.slogan;
						this.resumo = obj.resumo;
						this.idGoogleAnalytics = obj.idGoogleAnalytics;
						this.missao = obj.missao;
						this.visao = obj.visao;
						this.valores = obj.valores;
						this.dominio = obj.dominio;
						this.telefone = obj.telefone;
						this.endereco = obj.endereco;
						this.emailContato = obj.emailContato;
						this.urlGoogleMaps = obj.urlGoogleMaps;
						this.detalhes = [];
						var self = this;
						if (obj.detalhes) {
							obj.detalhes.forEach(function (detalhe) {
								self.detalhes.push(new Detalhe(detalhe));
							});
						}
					}
				}

				Empresa.load = function () {
					return Server.get();
				}

				Empresa.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}

				return Empresa;
			}]);