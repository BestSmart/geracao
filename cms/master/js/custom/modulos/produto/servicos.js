App
		.factory('Produto', ['$resource', 'Detalhe', function ($resource, Detalhe) {
				var Server = $resource('rest/index.php/produto/:id/')
				function Produto(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.nome = obj.nome;
						this.conteudo = obj.conteudo;
						this.resumo = obj.resumo;
						this.tags = obj.tags;
						this.destaque = obj.destaque ? true : false;
						this.album = obj.album;
						this.detalhes = [];
						var self = this;
						if (obj.detalhes) {
							obj.detalhes.forEach(function (detalhe) {
								self.detalhes.push(new Detalhe(detalhe));
							});
						}
					}
				}

				Produto.load = function () {
					return Server.query();
				}

				Produto.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}
				Produto.prototype.remover = function () {
					var self = this;
					return Server.remove(this);
				}

				return Produto;
			}]);