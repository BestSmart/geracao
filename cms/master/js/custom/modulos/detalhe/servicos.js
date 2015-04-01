App
.factory('Detalhe', ['$resource',function($resource){
        var Server = $resource('rest/index.php/detalhe/:id/')
	function Detalhe (obj){
		if (obj && (typeof obj !== undefined)) {
			this.id = obj.id;
			this.label = obj.label;
			this.conteudo = obj.conteudo;
		}
	}
        
        Detalhe.load = function(){
            return Server.query();
        }
        
        Detalhe.prototype.salvar = function(){
           var self = this;
           return Server.save(self);
        }
        Detalhe.prototype.remover = function(){
           var self = this;
           return Server.remove({'id':this.id});
        }

	return Detalhe;
}]);