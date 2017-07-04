/*
 * 我的支付
 */

var goToPayApp = angular.module("goToPayApp",[]);

goToPayApp.run(function(){
	FastClick.attach(document.body); 
});

goToPayApp.controller("ctrl",function($scope){
	
	goToPayAppController.init($scope);
});

var goToPayAppController = 
{
	scope : null,
	
	comfromSpecial : null,
	
	payPageTitle : '',
	
	
	init : function($scope){
		
		this.scope = $scope;
		
		this.getURLData();
		
		this.getgoToPayAppData();
		
		this.eventBind();
	},
	
	
	getURLData : function(){
		var self = this;
		
		var obj = new Base64();
//		alert(333)
		
		self.comfromSpecial =  obj.decode(commonFu.getQueryStringByKey("comfromSpecial"));
//		alert(self.comfromSpecial+1)
//      alert(self.comfromSpecial)
		if(self.comfromSpecial == 3)
		{  
			self.payPageTitle = "订单创建成功！请添加下方微信号:";
			$(".to-see-order-detail-two").css("display","block")
		}
		else
		{
			self.payPageTitle = "线下支付";
			$(".to-see-order-detail-two").css("display","none")
			
		}
		self.scope.comfromSpecial = self.comfromSpecial;
		self.scope.payPageTitle = self.payPageTitle;
		
	},
	
	
	getgoToPayAppData : function(){
		var self = this;
		
	},
	
	
	eventBind : function(){
		var self = this;
		
		self.scope.gotoOrderDetail = function(){
			
			var href_id = localStorage.getItem("toseeOrder");
			localStorage.setItem("comewidthgoto",2);
			localStorage.setItem(localStorageKey.orderNo,href_id);
	       		setTimeout(function(){
	       		 	
        			location.href = pageUrl.ORDER_DETAIL;
        				
        	},250)
		};
		
	},
	
};