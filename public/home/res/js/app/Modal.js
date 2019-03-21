var rNod=$('body div.modal');

export var Modal={
	_node:{
		root:rNod,
		size:rNod.find('.modal-dialog'),
		head:rNod.find('.modal-header'),
		load:rNod.find('.content'),
		title:rNod.find('.title')
	},
	optDefault:{headBg:'',title:''},
	size:'modal-sm',
	headBg:'',
	title:'',
	small:function(cont='',opt={}){
		let self=this;
		opt=$.extend({},self.optDefault,opt);
		
		self.headBg=opt.headBg;
		self.title=opt.title;
		self.size='modal-sm';
		
		return self.show(cont);
	},
	large:function(cont='',opt={}){
		let self=this;
		
		opt=$.extend({},self.optDefault,opt);
		
		self.headBg=opt.headBg;
		self.title=opt.title;
		self.size='modal-lg';
		
		return self.show(cont);
	},
	show:function(cont=''){
		let self=this;
		let node=self._node;
		let headCls=self.headBg;
		
		node.size.attr('class','modal-dialog');
		node.head.attr('class','modal-header');
		
		node.head.hide();
		if(headCls){
			node.head.show().addClass(headCls);
		}
		
		node.title.empty().append(self.title);
		node.size.addClass(self.size);
		node.load.empty().append(cont);
		
		node.root.modal();
	},
	//run ONCE for each handler
	addEvent:function(handler='',callBack){
		let self=this;
		let modal=self._node.root;
		let handlerDefault=['show.bs.modal','shown.bs.modal','hide.bs.modal','hidden.bs.modal'] ;	
 	
 		if(handlerDefault.indexOf(handler)!=-1){
			return modal.one(handler,callBack);
		}
		return console.log('wrong modal handler:'+handler);
		
	}
	
}

