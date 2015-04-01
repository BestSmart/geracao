App
		.factory('Pagina', ['$resource', function ($resource) {
				var Server = $resource('rest/index.php/pagina/:id/');
				function Pagina(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.titulo = obj.titulo;
						this.nomeMenu = obj.nomeMenu;
						this.ordem = obj.ordem;
						this.conteudo = obj.conteudo;
						this.publicar = obj.publicar ? true : false;
						this.postagem = obj.postagem ? true : false;
						this.dataHora = obj.dataHora;
						this.album = obj.album;
						this.tags = obj.tags;
						this.resumo = obj.resumo;
						this.url = 'http://bestsmart.com.br/post/' + this.titulo;
					}
				}

				Pagina.load = function () {
					return Server.query();
				}

				Pagina.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}
				Pagina.prototype.remover = function () {
					var self = this;
					return Server.remove({'id': this.id});
				}
				return Pagina;
			}]);