App
		.factory('Imagem', ['$resource', function ($resource) {
				var Server = $resource('rest/index.php/imagem/:album/:id');
				function Imagem(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.url = obj.url;
						this.thumbnail = obj.thumbnail;
						this.capa = obj.capa ? true : false;
						this.album = obj.album;
					}
				}

				Imagem.load = function (album) {
					return Server.query({
						'album':album
					});
				};

				Imagem.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				};
				Imagem.prototype.setCapa = function () {
					var self = this;
					return Server.save({
						album : 'capa',
						id : self.id
					},{});
				};
				Imagem.prototype.remover = function () {
					var self = this;
					return Server.remove({album : self.id});
				};

				return Imagem;
			}]);