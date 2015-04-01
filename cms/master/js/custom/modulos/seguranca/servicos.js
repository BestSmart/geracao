App.factory('SessionService', ['$http', function($http) {
	var user = undefined;
	var menus = undefined;
	var permissao = undefined;
	return {
		setUser : function(_user) {
			this.user = _user;
		},
		getUser : function() {
			return this.user;
		},
		reloadUser : function(){
			return $http.get('rest/index.php/credencial').success(function(ret){
				this.user = ret.data;
				return ret.data;
			});
		},
		getMenus : function(){
			return $http.get('server/sidebar-menu.json').success(function(data){
				this.menus = data;
				return data;
			})
		},
		setPermissao : function(_permissao){
			this.permissao = _permissao;
		},
		getPermissao : function(){
			return this.permissao;
		}
	};
} ])
.factory('Authorizer', [ function() {
	function Authorizer(user) {
		this.user = user;
	}

	Authorizer.prototype.canAccessUrl = function(url) {
		return true;
	}
	return Authorizer;
} ])
.factory('LoginService',['$http','authService',function($http, authService) {
	function serializeData(data) {
		if (!angular.isObject(data)) {
			return ((data === null) ? "" : data
					.toString());
		}
		var buffer = [];
		for ( var name in data) {
			if (!data.hasOwnProperty(name)) {
				continue;
			}
			var value = data[name];

			buffer
			.push(encodeURIComponent(name)
					+ "="
					+ encodeURIComponent((value === null) ? ""
							: value));
		}
		var source = buffer.join("&").replace(/%20/g,
		"+");
		return (source);
	}
	return {
		logout : function(callback){
			$http
			.post('rest/index.php/logout')
			.success(function(data) {
				callback && callback(true, data);
			}).error(function(data){
				callback && callback(false, data);
			});
		},
		recuperarSenha : function(credencial, callback){
			$http
			.post('rest/index.php/recuperarSenha/',credencial)
			.success(function(data) {
				callback && callback(true, data);
			}).error(function(data){
				callback && callback(false, data);
			});
		},
		login : function(credenciais, callback) {
			var falhaCallback = function(data) {
				callback && callback(false, data);
			};
			var sucessoCallback = function(data) {
				$http
				.get('rest/index.php/credencial')
				.success(
						function(data) {
							authService
							.loginConfirmed(data);
							callback
							&& callback(
									true,
									data);
						}).error(falhaCallback);
			};
			$http
			.post('rest/index.php/login',credenciais)
					.success(sucessoCallback)
					.error(falhaCallback);
		}
	};
} ])
.factory('Permissao', ['$resource','$state','Config','SessionService','$http','Empresa','Perfil','Credencial',function ($resource, $state, Config, SessionService, $http, Empresa, Perfil, Credencial) {
	var Server = $resource(
	'acesso/permissao/:id/:pageNumber/:pageSize');

	function Permissao(obj) {
		if (obj !== undefined) {
			this.id = obj.id;
			this.empresa = new Empresa(obj.empresa);
			this.perfil = new Perfil(obj.perfil);
			this.credencial = new Credencial(obj.credencial);
		}
	}
	
	Permissao.get = function (_id) {
		var permissao = Server.get({
			id: _id
		});
		var obj = new Permissao(permissao);
		return obj;
	};
	
	Permissao.prototype.remover = function(){
		var self = this;
		return Server.remove({'id':self.id});
	}
	Permissao.prototype.entrar = function () {
		Server.save({id: this.id}, {}).$promise.then(function (data) {
			$http.get('user').success(function(data){
				SessionService.setUser(data);
				$state.go('principal');
			})
		},function (data) {
			$state.go('login');
		});
	};
	Permissao.load = function (_pageNumber, _pageSize, sorting, filter) {
		return Server.save({
			pageNumber: _pageNumber,
			pageSize: _pageSize
		}, {'sorting': sorting, 'filter': {'search':filter}}, function (data) {
			data.content.forEach(function (current, index) {
				data.content[index] = new Permissao(current);
			});
		});
	};
	Permissao.loadByUsuario = function (_usuarioId, _pageNumber, _pageSize, sorting, filter) {
		return $resource(
		'acesso/permissaoPorUsuario/:usuarioId/:pageNumber/:pageSize').save({
			usuarioId : _usuarioId,
			pageNumber: _pageNumber,
			pageSize: _pageSize,
			
		}, {'sorting': sorting, 'filter': {'search':filter}}, function (data) {
			if(data && data.content && (typeof data.content !== undefined)){
				data.content.forEach(function (current, index) {
					data.content[index] = new Permissao(current);
				});
			}
		});
	};
	return Permissao;
}]);