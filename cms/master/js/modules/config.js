/**=========================================================
 * Module: config.js
 * App routes and resources configuration
 =========================================================*/

App.config(['$stateProvider', '$urlRouterProvider', '$controllerProvider', '$compileProvider', '$filterProvider', '$provide', '$ocLazyLoadProvider', 'APP_REQUIRES',
	function ($stateProvider, $urlRouterProvider, $controllerProvider, $compileProvider, $filterProvider, $provide, $ocLazyLoadProvider, appRequires) {
		'use strict';

		App.controller = $controllerProvider.register;
		App.directive = $compileProvider.directive;
		App.filter = $filterProvider.register;
		App.factory = $provide.factory;
		App.service = $provide.service;
		App.constant = $provide.constant;
		App.value = $provide.value;
		App.stateProvider = $stateProvider;
		App.urlRouterProvider = $urlRouterProvider;

		// LAZY MODULES
		// ----------------------------------- 
		$ocLazyLoadProvider.config({
			debug: false,
			events: true,
			modules: appRequires.modules
		});

		// Set here the base of the relative path
		// for all app views
		App.basepath = function (uri) {
			return 'app/views/' + uri;
		}

		// Generates a resolve object by passing script names
		// previously configured in constant.APP_REQUIRES
		App.resolveFor = function () {
			var _args = arguments;
			return {
				deps: ['$ocLazyLoad', '$q', function ($ocLL, $q) {
						// Creates a promise chain for each argument
						var promise = $q.when(1); // empty promise
						for (var i = 0, len = _args.length; i < len; i++) {
							promise = andThen(_args[i]);
						}
						return promise;

						// creates promise to chain dynamically
						function andThen(_arg) {
							// also support a function that returns a promise
							if (typeof _arg == 'function')
								return promise.then(_arg);
							else
								return promise.then(function () {
									// if is a module, pass the name. If not, pass the array
									var whatToLoad = getRequired(_arg);
									// simple error check
									if (!whatToLoad)
										return $.error('Route resolve: Bad resource name [' + _arg + ']');
									// finally, return a promise
									return $ocLL.load(whatToLoad);
								});
						}
						// check and returns required data
						// analyze module items with the form [name: '', files: []]
						// and also simple array of script files (for not angular js)
						function getRequired(name) {
							if (appRequires.modules)
								for (var m in appRequires.modules)
									if (appRequires.modules[m].name && appRequires.modules[m].name === name)
										return appRequires.modules[m];
							return appRequires.scripts && appRequires.scripts[name];
						}

					}]};
		}

		$urlRouterProvider.otherwise('/principal');

		$stateProvider.state('login', {
			url: '/login',
			templateUrl: 'app/pages/login.html',
			resolve: App.resolveFor('taginput', 'inputmask', 'parsley', 'icons', 'animo', 'ngDialog'),
			controller: 'LoginCtrl'
		})
				.state('principal', {
					url: '/principal',
					templateUrl: App.basepath('principal.html'),
					controller: 'PrincipalCtrl',
					resolve: App.resolveFor('xeditable','ngDialog', 'light-gallery', 'angularFileUpload', 'summernote', 'localytics.directives', 'taginput', 'inputmask', 'parsley', 'datatables', 'datatables-pugins', 'ngTable', 'ngTableExport', 'fastclick', 'modernizr', 'icons', 'screenfull', 'animo', 'sparklines', 'slimscroll', 'classyloader', 'toaster', 'whirl', 'angularBootstrapNavTree', 'filestyle')
				})
				.state('principal.empresa', {
					url: '/empresa',
					title: 'Empresa',
					templateUrl: App.basepath('empresa/empresa.html'),
					controller: 'EmpresaCtrl'
				})
				.state('principal.credencial', {
					url: '/credencial',
					title: 'Credencial',
					templateUrl: App.basepath('acesso/credencial.html'),
					controller: 'CredencialCtrl'
				})
				.state('principal.pagina', {
					url: '/pagina',
					title: 'Página',
					templateUrl: App.basepath('pagina/pagina.html'),
					controller: 'PaginaCtrl'
				})
				.state('principal.produto', {
					url: '/produto',
					title: 'Produto',
					templateUrl: App.basepath('produto/produto.html'),
					controller: 'ProdutoCtrl'
				})
				.state('principal.parceiro', {
					url: '/parceiro',
					title: 'Parceiro',
					templateUrl: App.basepath('parceiro/parceiro.html'),
					controller: 'ParceiroCtrl'
				})
				.state('principal.detalhe', {
					url: '/detalhe',
					title: 'Detalhe',
					templateUrl: App.basepath('detalhe/detalhe.html'),
					controller: 'DetalheCtrl'
				})
				.state('principal.sessao', {
					url: '/sessao',
					title: 'Sessão',
					templateUrl: App.basepath('sessao/sessao.html'),
					controller: 'SessaoCtrl'
				})
				.state('principal.album', {
					url: '/album',
					title: 'Album',
					templateUrl: App.basepath('album/album.html'),
					controller: 'AlbumCtrl'
				})
				.state('principal.carrosel', {
					url: '/carrosel',
					title: 'Carrosel',
					templateUrl: App.basepath('carrousel/carrousel.html'),
					controller: 'CarrouselCtrl'
				})
				.state('principal.alterarSenha', {
					url: '/alterarSenha',
					title: 'Alterar Senha',
					templateUrl: App.basepath('acesso/alterarSenha.html'),
					controller: 'AlterarSenhaCtrl'
				})
				;



	}]).config(['$translateProvider', function ($translateProvider) {

		$translateProvider.useStaticFilesLoader({
			prefix: 'app/i18n/',
			suffix: '.json'
		});
		$translateProvider.preferredLanguage('en');
		$translateProvider.useLocalStorage();

	}]).config(['cfpLoadingBarProvider', function (cfpLoadingBarProvider) {
		cfpLoadingBarProvider.includeBar = true;
		cfpLoadingBarProvider.includeSpinner = false;
		cfpLoadingBarProvider.latencyThreshold = 500;
		cfpLoadingBarProvider.parentSelector = '.wrapper > section';
	}])
		.controller('NullController', function () {
		});
