App
		.factory('Parceiro', ['$resource', function ($resource) {
				var Server = $resource('rest/index.php/parceiro/:id/');
				function Parceiro(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.nome = obj.nome;
						this.url = obj.url;
						this.slogan = obj.slogan;
						this.album = obj.album;
					}
				}

				Parceiro.load = function () {
					return Server.query();
				}

				Parceiro.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}
				Parceiro.prototype.remover = function () {
					var self = this;
					return Server.remove(this);
				}

				return Parceiro;
			}]);