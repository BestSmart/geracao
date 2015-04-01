App
.factory('Sessao', ['$resource',function($resource){
        var Server = $resource('rest/index.php/sessao/:id/')
	function Sessao (obj){
		if (obj && (typeof obj !== undefined)) {
			this.id = obj.id;
			this.codigo = obj.codigo;
			this.conteudo = obj.conteudo;
		}
	}
        
        Sessao.load = function(){
            return Server.query();
        }
        
        Sessao.prototype.salvar = function(){
           var self = this;
           return Server.save(self);
        }
        Sessao.prototype.remover = function(){
           var self = this;
           return Server.remove(this);
        }

	return Sessao;
}]);