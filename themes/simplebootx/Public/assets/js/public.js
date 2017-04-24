$(function(){
/*****************************************主导航被选中********************************/
	 $(".nav a").each(function(){  
				        $this = $(this);  
				        if($this[0].href==String(window.location)){  
				            $this.addClass("on");  
				        }  
				    });  


//*************************************************选中主导航背景颜色渐变
	
	var a=$(".nav a").not(".on");
	a.mouseover(function(){
		$(this).css("color","#FFFFFF").siblings().animate({"opacity":1},300);
	}).mouseout(function(){
		$(this).css("color","#333").siblings().animate({"opacity":0},300)
	});

//***************************************************banner 文字变化

move();
function move(){
	$(".bannerWord").animate({"bottom":"6rem","opacity":1},1000)
};


//****************************************************点击更多

$(".more").mouseover(function(){
	$(this).stop().animate({"width":"120px"},300)
	$(this).find(".iconfont").stop().animate({"opacity":"1"},200)
}).mouseout(function(){
	$(this).stop().animate({"width":"100px"},200)
	$(this).find(".iconfont").stop().animate({"opacity":"0"},100)
});

//*****************************************************首页自我介绍鼠标滑过的变化
$(".Img").mouseover(function(){
	$(this).find("a").stop().fadeIn(200);
	$(this).find("div").stop().animate({"opacity":1,"bottom":"30px"},200)

}).mouseout(function(){
	$(this).find("a").stop().fadeOut(200);
	$(this).find("div").stop().animate({"opacity":0,"bottom":"0px"},200);
});


//****************************************************联系方式文字出现
$(window).scroll(function(){
var ct=$(".contactcenter").offset().top+420;
var wh=$(window).height();
var wt=$(window).scrollTop();
var all=wt+wh;
	if(ct<=all){
		$(".contactCon").animate({"bottom":"120px","opacity":"1"},1000); 
	}
})

//******************************************************秀场切换
	 $(".fash li").eq(0).addClass("red").siblings().removeClass("red");
	$(".showOut>div").eq(0).show().siblings().hide();
  $(".fash li").click(function(){
    var num =$(".fash li").index(this);
	$(this).addClass("red").siblings().removeClass("red");
    var ou=$(".showOut>div").eq(num);
    ou.show().siblings().hide();
    var pic=ou.find(".fashion");
    console(pic);
    ou.siblings().find(".fashion").attr("id","");
   
  })

//****************************************************试用中心切换
$(".past,.report").hide();

$(".btn1").click(function(){
	$(this).addClass("org").siblings().removeClass("org");
	$(".trying").show().siblings().hide();
})
$(".btn2").click(function(){
	$(this).addClass("org").siblings().removeClass("org");
	$(".past").show().siblings().hide();
});
$(".btn3").click(function(){
	$(this).addClass("org").siblings().removeClass("org");
	$(".report").show().siblings().hide();
});


/********************订单页删除订单*******************/
	$("section").on("click",".delOr",function(){
		$(this).parents("section").remove();
	})
	
	/*******************************添加新地址*****************/
	//鼠标点击添加新地址
	$(".addBtn").click(function(){
		$(".mask").show();
	});
	//鼠标点击取消
	$(".cancel").click(function(){
		$(".mask").hide();
	});
	
	//删除地址
	$(".par").on("click",".del",function(){
		$(this).parents(".par").remove();
	});
	
	//编辑地址
	$(".edit").click(function(){
		$(".mask").show();
	});
	
	
	//默认地址选中状态
	$("#addr> div:first-child").find("p:last-child").show();
	$(".content").on("click","#addr> div",function(){
		$(this).addClass("on").siblings().removeClass("on").siblings().find("p:last-child").hide();
		$(this).find("p:last-child").show().parent().siblings().find("p:last-child").hide();
	});
	/**************************我的订单*************************/
	
	
	
	
	//	侧导航切换
	var le=$(".child li").size();
	for(var i=0;i<le;i++){
		var lti=$(".child li").eq(i).find("a").text();
		
		var rh=$("article>h2").find("span").text();
		if(lti==rh){
			$(".child li").eq(i).addClass("choice");
		}
		else{
			$(".child li").eq(i).removeClass("choice");
		}
	};
	
	/*菜单折叠*/
	function hi(){
		for (var i=0;i<$(".child a").size();i++) {
			 var lte=$(".child").find("a").eq(i).text();
			 
			 var rhe=$("article>h2").find("span").text();
			 if(lte==rhe){
			 	$(".child").find("a").eq(i).closest(".child").show();
			 	$(".child").find("a").eq(i).closest(".rig").removeClass("rig").addClass("up");
			 }
		}
	}
	hi();	
	$(".orTit>li:has('ul')").children("a").click(function(){
		$(this).parent().toggleClass("rig").toggleClass("up").find("ul").slideToggle();
	});
	
	/*确认收货弹框*/
	$("section").on("click",".confirm",function(){
		$(".payzz").show();
	})
	$(".payzz").on("click",".dnone",function(){
		$(".payzz").hide();
	})

//已收藏
 $("#collect").click(function(){
 	$(this).text("已收藏");
 	
 })

});
	