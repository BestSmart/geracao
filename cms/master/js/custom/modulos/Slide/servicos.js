App
		.factory('Slide', ['$resource', function ($resource) {
				var Server = $resource('rest/index.php/slide/:id/')
				function Slide(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.linkImagem = obj.linkImagem;
						this.ordem = obj.ordem;
						this.texto = obj.texto;
						this.titulo = obj.titulo;
						this.redirect = obj.redirect;
					}
				}

				Slide.load = function () {
					return Server.query();
				}

				Slide.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}
				Slide.prototype.remover = function () {
					var self = this;
					return Server.remove({'id': this.id});
				}

				return Slide;
			}]);