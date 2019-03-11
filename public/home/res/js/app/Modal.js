var rNod=$('body div.modal');

export var Modal={
	_node:{
		root:rNod,
		size:rNod.children(),
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
		
		if(headCls){
			node.head.show().addClass(headCls);
		}else{
			node.head.hide();
		}
		node.title.html(self.title);
		node.size.addClass(self.size);
		node.load.html(cont);
		
		node.root.modal();
	},
	
}

