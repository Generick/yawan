/**
 *  config route
 */

app.config(function($stateProvider, $urlRouterProvider){

	$urlRouterProvider.otherwise('/content');

	$stateProvider
	    .state('content',{
	    	url: '/content',
            templateUrl: 'view/content/mn.content.html',
            controller: 'ContentCtrl'
	    })
        .state('goods', {
            url: '/goods',
            templateUrl: 'view/goods/mn.goods.html',
            controller: 'GoodsCtrl'
        })
        .state('auction', {
            url: '/auction',
            templateUrl: 'view/auction/mn.auction.html',
            controller: 'AuctionCtrl'
        })
        .state('bidding', {
            url: '/bidding',
            templateUrl: 'view/auction/biddingList.html',
            controller: 'BiddingCtrl'
        })
        .state('admin', {
            url: '/admin',
            templateUrl: 'view/admin/mn.admin.html',
            controller: 'AdminCtrl'
        })
        .state('user', {
            url: '/user',
            templateUrl: 'view/user/mn.user.html',
            controller: 'UserCtrl'
        })
        .state('withdraw', {
            url: '/withdrew',
            templateUrl: 'view/withdraw/mn.withdraw.html',
            controller: 'WithdrawCtrl'
        })
        .state('order', {
            url: '/order',
            templateUrl: 'view/order/mn.order.html',
            controller: 'OrderCtrl'
        })
        .state('saleData', {
            url: '/saleData',
            templateUrl: 'view/data/mn.data.sale.html',
            controller: 'SaleDataCtrl'
        })
        .state('rechargeData', {
            url: '/rechargeData',
            templateUrl: 'view/data/mn.data.recharge.html',
            controller: 'RechargeDataCtrl'
        })
        .state('goodsData', {
            url: '/goodsData',
            templateUrl: 'view/data/mn.data.goods.html',
            controller: 'GoodsDataCtrl'
        })
        .state('balanceData', {
        	url: '/balanceData',
        	templateUrl: 'view/data/mn.data.balance.html',
        	controller: 'BalanceDataCtrl'
        })
});