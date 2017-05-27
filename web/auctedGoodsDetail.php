<?php
    require_once "wx/jssdk.php";
	$jssdk = new JSSDK("wx8aa4883c737caaaa", "620937dd20bdecf9e84f369d2ef64305");
	$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<html ng-app="app">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <title></title>
        <link rel="stylesheet" href="swiper/swiper.css" />
        <link rel="stylesheet" href="css/base.css" />
        <link rel="stylesheet" href="css/selected.css" />
        <link rel="stylesheet" href="popUpModal/popUp.css" />
        <link rel="stylesheet" href="plugin/layerMobile/need/layer.css" />
	</head>
	<body ng-controller="ctrl">
		<!--加载动画-->
		<div class="animation">
			<img src="img/loading.gif" />
		</div>
		<div class="container" style="overflow-y: scroll;padding-bottom: 65px;">
			<!-- Swiper -->
		    <div class="swiper-container">
		        <div class="swiper-wrapper">
		            <div class="swiper-slide item-img" ng-repeat="item in auctedGoodsDetailModel.allInfo.goodsInfo.goods_pics" on-Finish-Render-Filters>
		            	<img ng-src="{{item}}">
		            </div>
		        </div>

                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>

			<div class="goods-content">
				<p id="goodsContent2"></p>
			</div>
			<div class="time-operate">
				<div style="text-align: center;color: #7E7346;font-size: 16px;padding: 12px 0;" ng-show="auctedGoodsDetailModel.auctionSuccess">
					已截拍，成交价<span style="color: #db3e1f;">￥</span>
					<span style="color: #db3e1f;" ng-bind="auctedGoodsDetailModel.allInfo.currentPrice"></span>
				</div>
				<div style="text-align: center;color: #7E7346;font-size: 16px;padding: 12px 0;" ng-show="!auctedGoodsDetailModel.auctionSuccess">
					已流拍
				</div>
				<button id="selfGoodsBtn" class="goods-btn-2" ng-show="auctedGoodsDetailModel.allInfo.isQuiz == '1'" ng-click="jumpToGuess(item)" >我要竞猜</button>
			</div>
			<div class="goods-operate groble-operate-thrity-h clear">
				<div class="goods-operate-block groble-thrity-h clear">
					<span class="operate-type">起</span>
					<div>
						￥<span id="initialPrice" ng-bind="auctedGoodsDetailModel.allInfo.initialPrice"></span>
					</div>
				</div>
				<div class="goods-operate-block groble-thrity-h">
					<span class="operate-type">加</span>
					<div>
						￥<span id="lowestPremium" ng-bind="auctedGoodsDetailModel.allInfo.lowestPremium"></span>
					</div>
				</div>
				<div class="goods-operate-block groble-thrity-h">
					<span class="operate-type">保</span>
					<div>
						￥<span ng-bind="auctedGoodsDetailModel.allInfo.margin"></span>
					</div>
				</div>
				<!--<div class="goods-operate-block" style="padding-left: 12%;">
					<span class="operate-type">延</span>
					<div>
						<span ng-bind="auctedGoodsDetailModel.allInfo.postponeTime"></span>分钟
					</div>
				</div>-->
			</div>
            <div class="goods-operate-box"  ng-show="auctedGoodsDetailModel.isShowHighest">
                <button class="goods-operate-block2">
                    <span class="reference-price">封顶价</span>￥<span ng-bind="auctedGoodsDetailModel.allInfo.cappedPrice"></span>
                </button>
            </div>
            
			<div class="goods-buyer">
				<div class="goods-buyer-block clear">
					<li class="clear" ng-repeat="item in biddingModel.biddingList">
						<img class="buyer-icon" ng-src="{{item.userData.smallIcon}}" />
						<div class="buyer-tel" ng-bind="item.userData.telephone"></div>
						<div class="buyer-money">￥<span ng-bind="item.nowPrice"></span></div>
						<div class="pay-time" ng-bind="item.createTime*1000 | date:'yy-MM-dd HH:mm:ss'"></div>
					</li>
					<div class="check-more" ng-click="onClickCheckMore()" ng-show="biddingModel.isCheckOver" ng-bind="checkMore">查看更多</div>
				</div>
			</div>
			
		</div>
		<!--底部tab-->
	    <div ng-include="'module/tab/tab.html'"></div>
	</body>
    
	<!--系统js-->
	<script type="text/javascript" src="js/weixin.js"></script> 
	<script type="text/javascript" src="js/jquery.min.js" ></script>
	<script type="text/javascript" src="js/angular.min.js" ></script>
	<script type="text/javascript" src="js/jqAjaxRequest.js" ></script>
	<script type="text/javascript" src="js/common.js" ></script>
	<script type="text/javascript" src="js/config.js" ></script>
	<script type="text/javascript" src="popUpModal/confirmTip.js" ></script>
    <script src="plugin/layerMobile/layer.js"></script>
    <script src="module/dialog/dialog.js"></script>
    <script src="controller/app.js"></script>
	
	
	<script src="js/commonArr.js"></script>
	<script src="module/tab/tab.js"></script>   
	
	<script>
		$(function(){
			
			//监听微信返回按钮
			pushHistory();
			var bool=false;  
            setTimeout(function(){  
                bool=true;  
            },1000);
		    window.addEventListener("popstate", function(e) {

		    	if (bool)
		    	{    
		    		if(sessionStorage.getItem("comeWithGuess") == 4)//表示从个人中心跳转过来的
			        {   
			        
			        	location.href = pageUrl.PERSON_CENTER;
			        }
			        else if(sessionStorage.getItem("comeWithGuess") == 5)//表示从个人信息跳转过来的
			        {   
			        	
			        	location.href = pageUrl.MY_MESSAGE;
			        }
		    		else
		    		{       
		    			    
		    			    sessionStorage.setItem("needPageId",1)
		    			    if(sessionStorage.getItem("messlistOrauction") == 0)//表示从拍卖历史跳转过来的
			        	    {   
			        	    	location.href = pageUrl.AUCTION_HISTORY + "?backPage=" + auctedGoodsDetailController.thisDetailPage + "&thisDataId=" + auctedGoodsDetailController.thisDataId;

			        	    }
			        	    else if(sessionStorage.getItem("messlistOrauction") == 1)//表示从从消息中心跳转过来的
			        	    {
			        	    	
			        	    	location.href = pageUrl.MY_MESSAGE;
			        	    }
			    			
			    		   
		    		}
		    	}
		    	pushHistory();
		        
		    }, false); 
		    function pushHistory(){
		        var state = {
		            title: "title",
		            url: "#"
		        }; 
		        window.history.pushState(state, "title", "#");
		    }
		})
	</script>
	<!--插件-->
	<script type="text/javascript" src="js/fastclick.js" ></script>
	<script type="text/javascript" src="swiper/swiper.min.js" ></script>
	<!--controller-->
	<script src="controller/auctedGoodsDetailController.js" ></script>
	<script>

		app.controller("ctrl", function ($scope)
		{
			
			sessionStorage.setItem("itIsAuctionPage",1);
		    
		    var wxParams = {};
	        wxParams.appId =  '<?php echo $signPackage["appId"];?>';
	        wxParams.timestamp =  '<?php echo $signPackage["timestamp"];?>';
	        wxParams.nonceStr =  '<?php echo $signPackage["nonceStr"];?>';
	        wxParams.signature =  '<?php echo $signPackage["signature"];?>';
	       
		    auctedGoodsDetailController.init($scope,wxParams); 
		   
		});

	</script>
	
</html>