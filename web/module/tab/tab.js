/**
 * Created by Administrator on 2017/1/4.
 */
var initTab = {
    /**
     * ��ʼ���ײ�����
     * @param $scope ������
     * @param page 0��ʾ�н����£�1������������2��ʾ������ʷ��3��ʾ�ҵ�
     */
    start: function($scope, page){
    	$scope.tabs = tab;
    	if(page != -1){
        	tab[page].isActive = true;
    	}
        
        $scope.onClickSwitchTab = function(index)
        {   
            //3����Ҫ�޸�index��ֵ
            if (index == 0)
            {        
		            	//  刷新  、跳转到指定位置【详情页】
		            	
		            	//刷新   非正在拍卖的详情页点击
						//跳转指定位置  正在拍卖的详情点击
            			if(!commonFu.isEmpty(sessionStorage.getItem("itIsSelectPage")))
            			{ 
            				
            				sessionStorage.removeItem("itIsAuctionPage")
	            			sessionStorage.removeItem("itIsSelectPage")
            				if(commonFu.isEmpty(sessionStorage.getItem("alreadyGet")))
            				{  
            				   
	            				if(!commonFu.isEmpty(GoodsInfoCtrl.thisDetailPage) && !commonFu.isEmpty(GoodsInfoCtrl.thisDataId))
	            				{    
	            					sessionStorage.setItem("needPage",1)
	            					location.href = pageUrl.SELECTED_GOODS  +"?thisDetailPage=" + GoodsInfoCtrl.thisDetailPage + "&thisDataId=" + GoodsInfoCtrl.thisDataId;
	            				}
	            				else
	            				{
	            				
	            					location.href = pageUrl.SELECTED_GOODS;
	            				}
            				}
            				else 
            				{    
                                
            					location.href = pageUrl.SELECTED_GOODS;
            				}
            			}
            			else
            			{  
            				sessionStorage.removeItem("itIsAuctionPage")
	            			sessionStorage.removeItem("itIsSelectPage")
            				location.href = pageUrl.SELECTED_GOODS;
            			}    			
            		
            	
            }
            else if (index == 1)
            {    
            	
            		    
            			if(!commonFu.isEmpty(sessionStorage.getItem("itIsAuctionPage")))
            			{    
            				sessionStorage.removeItem("itIsAuctionPage")
	            			sessionStorage.removeItem("itIsSelectPage")
            				if(commonFu.isEmpty(sessionStorage.getItem("hasGetData")))
            				{  
            				    
	            			    if(!commonFu.isEmpty(auctedGoodsDetailController.thisDetailPage) && !commonFu.isEmpty(auctedGoodsDetailController.thisDataId))
	            				{   
	            				    sessionStorage.setItem("needPageId",1)
	            					location.href = pageUrl.AUCTION_HISTORY  +"?thisDetailPage=" + auctedGoodsDetailController.thisDetailPage + "&thisDataId=" + auctedGoodsDetailController.thisDataId;
	            				}
	            				else
	            				{   
                                   
	            					location.href = pageUrl.AUCTION_HISTORY;
	            				}
	            				
	            			}
	            			else 
	            			{    
	            	          
	            				location.href = pageUrl.AUCTION_HISTORY;
	            			}
            			}
            			else
            			{  
            				sessionStorage.removeItem("itIsAuctionPage")
	            			sessionStorage.removeItem("itIsSelectPage")
            				location.href = pageUrl.AUCTION_HISTORY;
            			}
            	
            }
            else if (index == 2)
            {   
                
                if(commonFu.isEmpty(localStorage.getItem(localStorageKey.TOKEN)))
				{   
					
                    location.href = pageUrl.LOGIN_PAGE;
				}
                else
                {   

					location.href = pageUrl.PERSON_CENTER;
                }

            }
        }
    }
};