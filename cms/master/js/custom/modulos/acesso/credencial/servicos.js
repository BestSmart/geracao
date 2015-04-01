App
		.factory('Credencial', ['$resource', function ($resource) {
				var Server = $resource('rest/index.php/credencial/:id/:senhaatual/:novasenha')
				function Credencial(obj) {
					if (obj && (typeof obj !== undefined)) {
						this.id = obj.id;
						this.bloqueado = obj.bloqueado;
						this.email = obj.email;
						this.expiraEm = obj.expiraEm;
						this.login = obj.login;
					}
				}

				Credencial.load = function () {
					return Server.query({id: "all"});
				}

				Credencial.prototype.salvar = function () {
					var self = this;
					return Server.save(self);
				}
				Credencial.prototype.remover = function () {
					var self = this;
					return Server.remove(self);
				}
				Credencial.prototype.novaSenha = function () {
					var self = this;
					return  $resource('rest/index.php/credencial/novaSenha').save(self);
				}
				Credencial.alterarSenha = function (senhaAtual, novaSenha, confirmarNovaSenha) {
					var self = {
						senhaAtual : senhaAtual,
						senha: novaSenha,
						confirmarSenha : confirmarNovaSenha
					};
					return  $resource('rest/index.php/credencial/alterarSenha').save(self);
				}

				return Credencial;
			}]);