App
		.factory('Carrousel', ['$resource', 'Slide', function ($resource, Slide) {
				var Server = $resource('rest/index.php/carrousel/')
				function Carrousel(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.slides = [];
						var self = this;
						if (obj.slides) {
							obj.slides.forEach(function (slide) {
								self.slides.push(new Slide(slide));
							});
						}
					}
				}

				Carrousel.load = function () {
					return Server.get();
				}

				Carrousel.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}

				return Carrousel;
			}]);