App
		.factory('Album', ['$resource', function ($resource) {
				var Server = $resource('rest/index.php/album/:id/')
				function Album(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.nome = obj.nome;
						this.descricao = obj.descricao;
						this.publicar = obj.publicar ? true : false;
					}
				}

				Album.load = function () {
					return Server.query();
				}

				Album.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}
				Album.prototype.remover = function () {
					var self = this;
					return Server.remove({'id': self.id});
				}

				return Album;
			}]);